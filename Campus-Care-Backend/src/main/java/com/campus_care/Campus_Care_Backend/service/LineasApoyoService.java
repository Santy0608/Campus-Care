package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.LineasApoyoDTO;

import javax.sound.sampled.Line;
import java.util.List;
import java.util.Optional;

public interface LineasApoyoService {

    List<LineasApoyoDTO> listadoLineasApoyo();

    Optional<LineasApoyoDTO> buscarLineaApoyoPorId(String id);

    LineasApoyoDTO agregarLineaApoyo(LineasApoyoDTO lineasApoyoDTO);

    LineasApoyoDTO actualizarLineaApoyo(LineasApoyoDTO lineasApoyoDTO, String id);

    void eliminarPorId(String id);

}
