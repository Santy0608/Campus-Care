package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.domain.TiposRecurso;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;
import com.campus_care.Campus_Care_Backend.repository.CategoriaRepository;
import com.campus_care.Campus_Care_Backend.repository.RecursoVectorRepository;
import com.campus_care.Campus_Care_Backend.repository.TiposRecursoRepository;
import com.campus_care.Campus_Care_Backend.service.ClaudeService;
import com.campus_care.Campus_Care_Backend.service.EmbeddingService;
import com.campus_care.Campus_Care_Backend.service.RecomendadorService;
import com.campus_care.Campus_Care_Backend.service.RecursoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;


@Service
public class RecomendadorServiceImpl implements RecomendadorService {

    @Autowired
    private EmbeddingService embeddingService;

    @Autowired
    private RecursoVectorRepository recursoVectorRepository;

    @Autowired
    private ClaudeService claudeService;

    @Autowired
    private CategoriaRepository categoriaRepository;

    @Autowired
    private TiposRecursoRepository tiposRecursoRepository;

    @Autowired
    private RecursoService recursoService;

    @Override
    public List<RecursoRecomendado> recomendar(Autoevaluacion autoevaluacion) {
        String textoConsulta = construirTextoConsulta(autoevaluacion);
        float[] queryEmbedding = embeddingService.generarEmbedding(textoConsulta);
        List<Recursos> candidatos = recursoVectorRepository.buscarSimilares(queryEmbedding, 5);

        // Claude decide el orden final / justificación, no solo el score coseno
        List<ClaudeService.RecomendacionInterna> refinadas =
                claudeService.refinarRecomendacion(autoevaluacion, candidatos);

        return convertirAPublico(refinadas);

    }

    private String construirTextoConsulta(Autoevaluacion autoevaluacion){
        return autoevaluacion.getRespuestas().stream()
                .map(r -> describirMetrica(r.getMetrica(), r.getScore()))
                .collect(Collectors.joining(". "));
    }

    private String describirMetrica(String metrica, Integer score){
        String nivel = switch (score) {
            case 1, 2 -> "bajo";
            case 3 -> "Medio";
            case 4, 5 -> "Alto";
            default -> "medio";
        };
        return "Nivel " + nivel + " en " + metrica;
    }

    private List<RecursoRecomendado> convertirAPublico(List<ClaudeServiceImpl.RecomendacionInterna> refinadas) {
        // Mapas construidos solo con las categorías/tipos de estos ~5 candidatos, no la colección completa
        Map<String, String> categoriasMap = categoriaRepository.findAllById(
                        refinadas.stream().map(r -> r.recurso().getCategoriaId()).distinct().toList())
                .stream().collect(Collectors.toMap(Categoria::getId, Categoria::getNombre));

        Map<String, String> tiposMap = tiposRecursoRepository.findAllById(
                        refinadas.stream().map(r -> r.recurso().getTipoRecursoId()).distinct().toList())
                .stream().collect(Collectors.toMap(TiposRecurso::getId, TiposRecurso::getNombre));

        return refinadas.stream()
                .map(r -> new RecursoRecomendado(
                        recursoService.convertirADTOListado(r.recurso(), categoriasMap, tiposMap),
                        r.razon()))
                .collect(Collectors.toList());
    }


}