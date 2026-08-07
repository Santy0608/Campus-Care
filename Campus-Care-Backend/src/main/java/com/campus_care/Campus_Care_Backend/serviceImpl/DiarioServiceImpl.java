package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Diario;
import com.campus_care.Campus_Care_Backend.dto.DiarioDTO;
import com.campus_care.Campus_Care_Backend.repository.DiarioRepository;
import com.campus_care.Campus_Care_Backend.service.DiarioService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.time.Instant;
import java.time.LocalDateTime;
import java.util.List;
import java.util.Map;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class DiarioServiceImpl implements DiarioService {

    @Autowired
    private DiarioRepository diarioRepository;

    @Override
    public List<DiarioDTO> listadoDiarios() {
        List<Diario> diarios = diarioRepository.findAll();
        List<DiarioDTO> dtos = diarios.stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public List<DiarioDTO> listadoDiarioPorUsuario(String idUsuario) {
        return diarioRepository.findByIdUsuario(idUsuario)
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
    }

    @Override
    public Optional<DiarioDTO> buscarDiarioPorId(String id) {
        return diarioRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public DiarioDTO agregarDiario(DiarioDTO diarioDTO) {
        Diario diario = new Diario();
        diario.setIdUsuario(diarioDTO.getIdUsuario());
        diario.setFecha(Instant.now());
        diario.setEntradaTexto(diarioDTO.getEntradaTexto());
        Diario diarioAgregado = diarioRepository.save(diario);
        return convertirADTO(diarioAgregado);
    }

    DiarioDTO convertirADTO(Diario diario){
        DiarioDTO dto = new DiarioDTO();
        dto.setId(diario.getId());
        dto.setIdUsuario(diario.getIdUsuario());
        dto.setFecha(diario.getFecha());
        dto.setEntradaTexto(diario.getEntradaTexto());
        return dto;
    }

}
