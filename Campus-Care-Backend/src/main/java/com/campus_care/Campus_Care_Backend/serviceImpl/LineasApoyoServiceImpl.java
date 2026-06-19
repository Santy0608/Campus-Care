package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.LineasApoyo;
import com.campus_care.Campus_Care_Backend.dto.LineasApoyoDTO;
import com.campus_care.Campus_Care_Backend.repository.LineasApoyoRepository;
import com.campus_care.Campus_Care_Backend.service.LineasApoyoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.web.bind.annotation.RequestMapping;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class LineasApoyoServiceImpl implements LineasApoyoService {

    @Autowired
    private LineasApoyoRepository lineasApoyoRepository;

    @Override
    public List<LineasApoyoDTO> listadoLineasApoyo() {
        List<LineasApoyo> lineasApoyos = lineasApoyoRepository.findAll();
        List<LineasApoyoDTO> dtos = lineasApoyos
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<LineasApoyoDTO> buscarLineaApoyoPorId(String id) {
        return lineasApoyoRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public LineasApoyoDTO agregarLineaApoyo(LineasApoyoDTO lineasApoyoDTO) {
        LineasApoyo lineasApoyo = new LineasApoyo();
        lineasApoyo.setNombreInstitucion(lineasApoyoDTO.getNombreInstitucion());
        lineasApoyo.setTelefono(lineasApoyoDTO.getTelefono());
        lineasApoyo.setHorarioAtencion(lineasApoyoDTO.getHorarioAtencion());
        lineasApoyo.setUrlSitio(lineasApoyoDTO.getUrlSitio());
        lineasApoyo.setActivo(lineasApoyoDTO.isActivo());
        LineasApoyo lineasApoyoAgregado = lineasApoyoRepository.save(lineasApoyo);
        return convertirADTO(lineasApoyo);
    }

    @Override
    public LineasApoyoDTO actualizarLineaApoyo(LineasApoyoDTO lineasApoyoDTO, String id) {
        return lineasApoyoRepository.findById(id)
                .map(lineasApoyo -> {
                    lineasApoyo.setId(lineasApoyoDTO.getId());
                    lineasApoyo.setNombreInstitucion(lineasApoyoDTO.getNombreInstitucion());
                    lineasApoyo.setTelefono(lineasApoyoDTO.getTelefono());
                    lineasApoyo.setHorarioAtencion(lineasApoyoDTO.getHorarioAtencion());
                    lineasApoyo.setUrlSitio(lineasApoyoDTO.getUrlSitio());
                    lineasApoyo.setActivo(lineasApoyoDTO.isActivo());
                    LineasApoyo lineasApoyoActualizado = lineasApoyoRepository.save(lineasApoyo);
                    return convertirADTO(lineasApoyoActualizado);
                })
                .orElseThrow(() -> new RuntimeException("Linea Apoyo no encontrado"));
    }

    @Override
    public void eliminarPorId(String id) {
        lineasApoyoRepository.findById(id);
    }

    LineasApoyoDTO convertirADTO(LineasApoyo lineasApoyo){
        LineasApoyoDTO dto = new LineasApoyoDTO();
        dto.setId(lineasApoyo.getId());
        dto.setNombreInstitucion(lineasApoyo.getNombreInstitucion());
        dto.setTelefono(lineasApoyo.getTelefono());
        dto.setHorarioAtencion(lineasApoyo.getHorarioAtencion());
        dto.setUrlSitio(lineasApoyo.getUrlSitio());
        dto.setActivo(lineasApoyo.isActivo());
        return dto;
    }

}
