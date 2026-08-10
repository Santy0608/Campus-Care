package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.*;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;
import com.campus_care.Campus_Care_Backend.repository.CategoriaRepository;
import com.campus_care.Campus_Care_Backend.repository.DiarioRepository;
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

    @Autowired
    private DiarioRepository diarioRepository;

    @Override
    public List<RecursoRecomendado> recomendar(Autoevaluacion autoevaluacion) {
        String entradaDiario = obtenerUltimaEntradaDiario(autoevaluacion.getIdUsuario());

        String textoConsulta = construirTextoConsulta(autoevaluacion);
        float[] queryEmbedding = embeddingService.generarEmbedding(textoConsulta);
        List<Recursos> candidatos = recursoVectorRepository.buscarSimilares(queryEmbedding, 5);

        // Claude decide el orden final / justificación, no solo el score coseno
        List<ClaudeService.RecomendacionInterna> refinadas =
                claudeService.refinarRecomendacion(autoevaluacion, candidatos, entradaDiario);

        return convertirAPublico(refinadas);

    }

    private String construirTextoConsulta(Autoevaluacion autoevaluacion){
        String textoMetricas = autoevaluacion.getRespuestas().stream()
                .map(r -> describirMetrica(r.getMetrica(), r.getScore()))
                .collect(Collectors.joining(". "));

        String entradaDiario = obtenerUltimaEntradaDiario(autoevaluacion.getIdUsuario());
        return entradaDiario.isEmpty()
                ? textoMetricas
                : textoMetricas + ". Contexto adicional del diario: " + entradaDiario;
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

    private String obtenerUltimaEntradaDiario(String idUsuario){
        return diarioRepository.findByIdUsuarioOrderByFechaDesc(idUsuario)
                .stream()
                .findFirst()
                .map(Diario::getEntradaTexto)
                .orElse("");
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