package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.EjercicioPractico;
import com.campus_care.Campus_Care_Backend.dto.EjercicioPracticoDTO;
import com.campus_care.Campus_Care_Backend.repository.EjercicioPracticoRepository;
import com.campus_care.Campus_Care_Backend.service.EjercicioPracticoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class EjercicioPracticoServiceImpl implements EjercicioPracticoService {

    @Autowired
    private EjercicioPracticoRepository ejercicioPracticoRepository;

    @Override
    public List<EjercicioPracticoDTO> listadoEjerciciosPracticos() {
        List<EjercicioPractico> ejercicioPracticos = ejercicioPracticoRepository.findAll();
        List<EjercicioPracticoDTO> dtos = ejercicioPracticos
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<EjercicioPracticoDTO> buscarEjercicioPracticoPorId(String id) {
        return ejercicioPracticoRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public EjercicioPracticoDTO guardarEjercicioPractico(EjercicioPracticoDTO ejercicioPracticoDTO) {
        EjercicioPractico ejercicioPractico = new EjercicioPractico();
        ejercicioPractico.setNombre(ejercicioPracticoDTO.getNombre());
        ejercicioPractico.setInstrucciones(ejercicioPracticoDTO.getInstrucciones());
        ejercicioPractico.setDuracionSegundos(ejercicioPracticoDTO.getDuracionSegundos());
        ejercicioPractico.setActivo(true);
        EjercicioPractico ejercicioPracticoAgregado = ejercicioPracticoRepository.save(ejercicioPractico);
        return convertirADTO(ejercicioPracticoAgregado);
    }

    @Override
    public EjercicioPracticoDTO actualizarEjercicioPractico(EjercicioPracticoDTO ejercicioPracticoDTO, String id) {
        return ejercicioPracticoRepository.findById(id)
                .map(ejercicioPractico -> {
                    ejercicioPractico.setId(ejercicioPracticoDTO.getId());
                    ejercicioPractico.setNombre(ejercicioPracticoDTO.getNombre());
                    ejercicioPractico.setInstrucciones(ejercicioPracticoDTO.getInstrucciones());
                    ejercicioPractico.setDuracionSegundos(ejercicioPracticoDTO.getDuracionSegundos());
                    ejercicioPractico.setActivo(true);
                    EjercicioPractico ejercicioPracticoActualizado = ejercicioPracticoRepository.save(ejercicioPractico);
                    return convertirADTO(ejercicioPracticoActualizado);
                })
                .orElseThrow(() -> new RuntimeException("Ejercicio Practico no encontrada con ID: " + id));

    }

    @Override
    public void eliminarEjercicioPractico(String id) {
        ejercicioPracticoRepository.deleteById(id);
    }

    EjercicioPracticoDTO convertirADTO(EjercicioPractico ejercicioPractico){
        EjercicioPracticoDTO dto = new EjercicioPracticoDTO();
        dto.setNombre(ejercicioPractico.getNombre());
        dto.setInstrucciones(ejercicioPractico.getInstrucciones());
        dto.setDuracionSegundos(ejercicioPractico.getDuracionSegundos());
        dto.setActivo(ejercicioPractico.isActivo());
        return dto;
    }


}
