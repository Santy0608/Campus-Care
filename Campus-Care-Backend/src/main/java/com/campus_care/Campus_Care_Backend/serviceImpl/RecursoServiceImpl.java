package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.domain.TiposRecurso;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;
import com.campus_care.Campus_Care_Backend.repository.CategoriaRepository;
import com.campus_care.Campus_Care_Backend.repository.RecursoRepository;
import com.campus_care.Campus_Care_Backend.repository.TiposRecursoRepository;
import com.campus_care.Campus_Care_Backend.service.EmbeddingService;
import com.campus_care.Campus_Care_Backend.service.RecursoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.Set;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class RecursoServiceImpl implements RecursoService {

    @Autowired
    private RecursoRepository recursoRepository;

    @Autowired
    private CategoriaRepository categoriaRepository;

    @Autowired
    private TiposRecursoRepository tiposRecursoRepository;

    @Autowired
    private EmbeddingService embeddingService;

    @Override
    public List<RecursosDTO> listadoRecursos() {
        List<Recursos> recursos = recursoRepository.findAll();

        Set<String> categoriaIds = recursos.stream()
                .map(Recursos::getCategoriaId)
                .collect(Collectors.toSet());

        Set<String> tipoIds = recursos.stream()
                .map(Recursos::getTipoRecursoId)
                .collect(Collectors.toSet());

        Map<String, String> categoriasMap = categoriaRepository.findAllById(categoriaIds)
                .stream()
                .collect(Collectors.toMap(Categoria::getId, Categoria::getNombre));

        Map<String, String> tiposMap = tiposRecursoRepository.findAllById(tipoIds)
                .stream()
                .collect(Collectors.toMap(TiposRecurso::getId, TiposRecurso::getNombre));

        return recursos.stream()
                .map(r -> convertirADTOListado(r, categoriasMap, tiposMap))
                .collect(Collectors.toList());
    }

    @Override
    public Optional<RecursosDTO> buscarRecursoPorId(String id) {
        return recursoRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public RecursosDTO agregarRecurso(RecursosDTO recursosDTO) {
        Recursos recurso = new Recursos();
        recurso.setTitulo(recursosDTO.getTitulo());
        recurso.setContenido(recursosDTO.getContenido());
        recurso.setUrlEnlace(recursosDTO.getUrlEnlace());

        Categoria categoria = categoriaRepository.findById(recursosDTO.getCategoriaId())
                .orElseThrow(() -> new RuntimeException("Categoría no encontrada"));

        TiposRecurso tiposRecurso = tiposRecursoRepository.findById(recursosDTO.getTipoRecursoId())
                .orElseThrow(() -> new RuntimeException("Tipos de Recurso no encontrado"));

        recurso.setCategoriaId(categoria.getId());
        recurso.setTipoRecursoId(tiposRecurso.getId());
        recurso.setFechaPublicacion(LocalDateTime.now());
        recurso.setActivo(recursosDTO.isActivo());

        String textoEmbedding = recurso.getTitulo() + ". " + recurso.getContenido();
        recurso.setEmbedding(embeddingService.generarEmbedding(textoEmbedding));

        Recursos recursoGuardado = recursoRepository.save(recurso);

        return convertirADTO(recursoGuardado);
    }

    @Override
    public RecursosDTO actualizarRecurso(RecursosDTO recursosDTO, String id) {
        return recursoRepository.findById(id)
                .map(recursos -> {
                    recursos.setId(recursosDTO.getId());
                    recursos.setTitulo(recursosDTO.getTitulo());
                    recursos.setContenido(recursosDTO.getContenido());
                    recursos.setUrlEnlace(recursosDTO.getUrlEnlace());

                    Categoria categoria = categoriaRepository.findById(recursosDTO.getCategoriaId())
                            .orElseThrow(() -> new RuntimeException("Categoría no encontrada"));

                    TiposRecurso tiposRecurso = tiposRecursoRepository.findById(recursosDTO.getTipoRecursoId())
                            .orElseThrow(() -> new RuntimeException("Tipos de Recurso no encontrado"));

                    recursos.setCategoriaId(categoria.getId());
                    recursos.setTipoRecursoId(tiposRecurso.getId());
                    recursos.setFechaPublicacion(LocalDateTime.now());
                    recursos.setActivo(recursosDTO.isActivo());

                    String textoEmbedding = recursos.getTitulo() + ". " + recursos.getContenido();
                    recursos.setEmbedding(embeddingService.generarEmbedding(textoEmbedding));

                    Recursos recursoActualizado = recursoRepository.save(recursos);
                    return convertirADTO(recursoActualizado);

                })
                .orElseThrow(() -> new RuntimeException("Recurso no encontrado " + id));
    }

    @Override
    public void elimianrRecursoPorId(String id) {
        recursoRepository.deleteById(id);
    }
    
    RecursosDTO convertirADTO(Recursos recursos){
        RecursosDTO dto = new RecursosDTO();
        dto.setId(recursos.getId());
        dto.setTitulo(recursos.getTitulo());
        dto.setContenido(recursos.getContenido());
        dto.setUrlEnlace(recursos.getUrlEnlace());

        Categoria categoria = categoriaRepository.findById(recursos.getCategoriaId()).orElse(null);
        dto.setCategoriaId(recursos.getCategoriaId());
        dto.setCategoriaNombre(categoria != null ? categoria.getNombre() : "Sin categoría");

        TiposRecurso tipo = tiposRecursoRepository.findById(recursos.getTipoRecursoId()).orElse(null);
        dto.setTipoRecursoId(recursos.getTipoRecursoId());
        dto.setTipoRecursoNombre(tipo != null ? tipo.getNombre() : "Sin tipo");

        dto.setFechaPublicacion(recursos.getFechaPublicacion());
        dto.setActivo(recursos.isActivo());
        return dto;
    }

    public RecursosDTO convertirADTOListado(Recursos recursos, Map<String, String> categoriasMap, Map<String, String> tiposMap) {
        RecursosDTO dto = new RecursosDTO();
        dto.setId(recursos.getId());
        dto.setTitulo(recursos.getTitulo());
        dto.setContenido(recursos.getContenido());
        dto.setUrlEnlace(recursos.getUrlEnlace());
        dto.setCategoriaId(recursos.getCategoriaId());
        dto.setCategoriaNombre(categoriasMap.getOrDefault(recursos.getCategoriaId(), "Sin categoría"));
        dto.setTipoRecursoId(recursos.getTipoRecursoId());
        dto.setTipoRecursoNombre(tiposMap.getOrDefault(recursos.getTipoRecursoId(), "Sin tipo"));
        dto.setFechaPublicacion(recursos.getFechaPublicacion());
        dto.setActivo(recursos.isActivo());
        return dto;
    }
}

