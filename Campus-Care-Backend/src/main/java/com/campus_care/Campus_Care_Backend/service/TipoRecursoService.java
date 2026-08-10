package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.TiposRecursoDTO;

import java.util.List;
import java.util.Optional;

public interface TipoRecursoService {

    List<TiposRecursoDTO> listadoTiposRecursos();

    Optional<TiposRecursoDTO> buscarTipoRecursoPorId(String id);

    TiposRecursoDTO agregarTipoRecurso(TiposRecursoDTO tiposRecursoDTO);

    TiposRecursoDTO actualizarTipoRecurso(TiposRecursoDTO tiposRecursoDTO, String id);

    void eliminarTipoRecurso(String id);


}
