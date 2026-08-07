package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.DiarioDTO;
import com.campus_care.Campus_Care_Backend.dto.UsuarioDTO;

import java.util.List;
import java.util.Optional;

public interface DiarioService {

    List<DiarioDTO> listadoDiarios();

    List<DiarioDTO> listadoDiarioPorUsuario(String idUsuario);

    Optional<DiarioDTO> buscarDiarioPorId(String id);

    DiarioDTO agregarDiario(DiarioDTO diarioDTO);



}
