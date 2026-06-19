package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.CatalogoLogrosDTO;

import java.util.List;
import java.util.Optional;

public interface CatalogoLogrosService {

    List<CatalogoLogrosDTO> listadoCatalogoLogros();

    Optional<CatalogoLogrosDTO> buscarCatalogoLogroPorId(String id);

    CatalogoLogrosDTO agregarCatalogoLogro(CatalogoLogrosDTO catalogoLogrosDTO);

    CatalogoLogrosDTO actualizarCatalogoLogro(CatalogoLogrosDTO catalogoLogrosDTO, String id);

    void eliminarCatalogoLogroPorId(String id);

}
