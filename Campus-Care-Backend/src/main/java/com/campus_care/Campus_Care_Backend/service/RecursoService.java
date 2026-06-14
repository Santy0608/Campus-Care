package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;

import java.util.List;
import java.util.Optional;

public interface RecursoService {

    List<RecursosDTO> listadoRecursos();

    Optional<RecursosDTO> buscarRecursoPorId(String id);

    RecursosDTO agregarRecurso(RecursosDTO recursosDTO);

    RecursosDTO actualizarRecurso(RecursosDTO recursosDTO, String id);

    void elimianrRecursoPorId(String id);

}
