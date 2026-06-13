package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.domain.EjercicioPractico;
import com.campus_care.Campus_Care_Backend.dto.EjercicioPracticoDTO;

import java.util.List;
import java.util.Optional;

public interface EjercicioPracticoService {

    List<EjercicioPracticoDTO> listadoEjerciciosPracticos();

    Optional<EjercicioPracticoDTO> buscarEjercicioPracticoPorId(String id);

    EjercicioPracticoDTO guardarEjercicioPractico(EjercicioPracticoDTO ejercicioPracticoDTO);

    EjercicioPracticoDTO actualizarEjercicioPractico(EjercicioPracticoDTO ejercicioPracticoDTO, String id);

    void eliminarEjercicioPractico(String id);

}
