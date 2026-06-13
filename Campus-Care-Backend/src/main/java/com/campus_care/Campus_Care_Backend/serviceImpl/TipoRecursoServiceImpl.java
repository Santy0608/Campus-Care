package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.TiposRecurso;
import com.campus_care.Campus_Care_Backend.dto.TiposRecursoDTO;
import com.campus_care.Campus_Care_Backend.repository.TiposRecursoRepository;
import com.campus_care.Campus_Care_Backend.service.TipoRecursoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class TipoRecursoServiceImpl implements TipoRecursoService {

    @Autowired
    private TiposRecursoRepository tiposRecursoRepository;

    @Override
    public List<TiposRecursoDTO> listadoTiposRecursos() {
        List<TiposRecurso> tiposRecursos = tiposRecursoRepository.findAll();
        List<TiposRecursoDTO> dtos = tiposRecursos
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<TiposRecursoDTO> buscarTipoRecursoPorId(String id) {
        return tiposRecursoRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public TiposRecursoDTO agregarTipoRecurso(TiposRecursoDTO tiposRecursoDTO) {
        TiposRecurso tiposRecurso = new TiposRecurso();
        tiposRecurso.setNombre(tiposRecursoDTO.getNombre());
        tiposRecurso.setDescripcion(tiposRecursoDTO.getDescripcion());
        TiposRecurso tipoRecursoAgregado = tiposRecursoRepository.save(tiposRecurso);
        return convertirADTO(tipoRecursoAgregado);
    }

    @Override
    public TiposRecursoDTO actualizarTipoRecurso(TiposRecursoDTO tiposRecursoDTO, String id) {
        return tiposRecursoRepository.findById(id).map(tiposRecurso -> {
            tiposRecurso.setId(tiposRecursoDTO.getId());
            tiposRecurso.setNombre(tiposRecursoDTO.getNombre());
            tiposRecurso.setDescripcion(tiposRecursoDTO.getDescripcion());
            TiposRecurso tipoRecursoActualizado = tiposRecursoRepository.save(tiposRecurso);
            return convertirADTO(tipoRecursoActualizado);
        }).orElseThrow(() -> new RuntimeException("Tipo Recurso no encontrada con ID: " + id));

    }

    @Override
    public void eliminarTipoRecurso(String id) {
        tiposRecursoRepository.deleteById(id);
    }

    TiposRecursoDTO convertirADTO(TiposRecurso tiposRecurso){
        TiposRecursoDTO dto = new TiposRecursoDTO();
        dto.setId(tiposRecurso.getId());
        dto.setNombre(tiposRecurso.getNombre());
        dto.setDescripcion(tiposRecurso.getDescripcion());
        return dto;
    }

}
