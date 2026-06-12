package com.campus_care.Campus_Care_Backend.service;


import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.dto.CategoriaDTO;

import java.util.List;
import java.util.Optional;

public interface CategoriaService {

    List<CategoriaDTO> listadoCategorias();

    Optional<CategoriaDTO> buscarCategoriaPorId(String idCategoria);

    CategoriaDTO guardarCategoria(CategoriaDTO categoriaDTO);

    CategoriaDTO actualizarCategoria(CategoriaDTO categoriaDTO, String idCategoria);

    void eliminarCategoriaPorId(String idCategoria);




}
