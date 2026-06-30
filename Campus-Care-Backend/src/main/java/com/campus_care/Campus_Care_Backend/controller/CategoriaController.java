package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.dto.CategoriaDTO;
import com.campus_care.Campus_Care_Backend.service.CategoriaService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/categorias")
@RequiredArgsConstructor
public class CategoriaController {

    @Autowired
    private CategoriaService categoriaService;

    @GetMapping("/listado-categorias")
    List<CategoriaDTO> listadoCategorias(){
        return categoriaService.listadoCategorias();
    }

    @GetMapping("/{id}")
    public ResponseEntity<CategoriaDTO> buscarCategoriaPorId(@PathVariable(name = "id") String id){
        Optional<CategoriaDTO> categoriaOptional = categoriaService.buscarCategoriaPorId(id);
        if (categoriaOptional.isPresent()){
            return ResponseEntity.ok(categoriaOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-categoria")
    public ResponseEntity<?> agregarCategoria(@RequestBody CategoriaDTO categoriaDTO){
        CategoriaDTO nuevaCategoria = categoriaService.guardarCategoria(categoriaDTO);
        return ResponseEntity.status(HttpStatus.CREATED).body(nuevaCategoria);
    }

    @PutMapping("/actualizar-categoria/{id}")
    public ResponseEntity<?> actualizarCategoria(@RequestBody CategoriaDTO categoriaDTO, @PathVariable String id){
        return ResponseEntity.ok(categoriaService.actualizarCategoria(categoriaDTO, id));
    }

    @DeleteMapping("/eliminar-categoria/{id}")
    public ResponseEntity<CategoriaDTO> eliminarCategoria(@PathVariable String id){
        Optional<CategoriaDTO> categoriaOptional = categoriaService.buscarCategoriaPorId(id);
        if (categoriaOptional.isPresent()){
            categoriaService.eliminarCategoriaPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }

}
