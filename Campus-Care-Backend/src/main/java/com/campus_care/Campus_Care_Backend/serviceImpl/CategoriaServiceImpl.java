package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.dto.CategoriaDTO;
import com.campus_care.Campus_Care_Backend.repository.CategoriaRepository;
import com.campus_care.Campus_Care_Backend.service.CategoriaService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class CategoriaServiceImpl implements CategoriaService {

    @Autowired
    private CategoriaRepository categoriaRepository;

    @Override
    public List<CategoriaDTO> listadoCategorias() {
        List<Categoria> categorias = categoriaRepository.findAll();
        List<CategoriaDTO> dtos = categorias.stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<CategoriaDTO> buscarCategoriaPorId(String idCategoria) {
        return categoriaRepository.findById(idCategoria).map(this::convertirADTO);
    }

    @Override
    public CategoriaDTO guardarCategoria(CategoriaDTO categoriaDTO) {
        Categoria categoria = new Categoria();
        categoria.setNombre(categoriaDTO.getNombre());
        categoria.setDescripcion(categoriaDTO.getDescripcion());
        Categoria categoriaAGuardar = categoriaRepository.save(categoria);
        return convertirADTO(categoriaAGuardar);
    }

    @Override
    public CategoriaDTO actualizarCategoria(CategoriaDTO categoriaDTO, String id) {
        return categoriaRepository.findById(id)
                .map(categoria -> {
                    categoria.setId(categoriaDTO.getId());
                    categoria.setNombre(categoriaDTO.getNombre());
                    categoria.setDescripcion(categoriaDTO.getDescripcion());

                    Categoria categoriaActualizada = categoriaRepository.save(categoria);

                    return convertirADTO(categoriaActualizada);
                })
                .orElseThrow(() -> new RuntimeException("Categoría no encontrada con ID: " + id));
    }

    @Override
    public void eliminarCategoriaPorId(String id) {
        categoriaRepository.deleteById(id);
    }

    CategoriaDTO convertirADTO(Categoria categoria){
        CategoriaDTO dto = new CategoriaDTO();
        dto.setId(categoria.getId());
        dto.setNombre(categoria.getNombre());
        dto.setDescripcion(categoria.getDescripcion());
        return dto;
    }

}
