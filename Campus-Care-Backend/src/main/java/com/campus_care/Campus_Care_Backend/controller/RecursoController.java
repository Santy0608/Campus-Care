package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;
import com.campus_care.Campus_Care_Backend.service.RecursoService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/recursos")
public class RecursoController {

    @Autowired
    private RecursoService recursoService;

    @GetMapping("/listado-recursos")
    public List<RecursosDTO> listadorRecursos(){
        return recursoService.listadoRecursos();
    }

    @GetMapping("/{id}")
    public ResponseEntity<RecursosDTO> buscarRecursoPorId(@PathVariable String id){
        Optional<RecursosDTO> recursosOptional = recursoService.buscarRecursoPorId(id);
        if (recursosOptional.isPresent()){
            return ResponseEntity.ok(recursosOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-recurso")
    public ResponseEntity<?> agregarRecurso(@RequestBody RecursosDTO recursosDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(recursoService.agregarRecurso(recursosDTO));
    }

    @PutMapping("/actualizar-recurso/{id}")
    public ResponseEntity<?> actualizarRecurso(@RequestBody RecursosDTO recursosDTO, @PathVariable String id){
        return ResponseEntity.ok(recursoService.actualizarRecurso(recursosDTO, id));
    }

    @DeleteMapping("/eliminar-recurso/{id}")
    public ResponseEntity<RecursosDTO> eliminarRecurso(@PathVariable String id){
        Optional<RecursosDTO> recursosOptional = recursoService.buscarRecursoPorId(id);
        if (recursosOptional.isPresent()){
            recursoService.elimianrRecursoPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }

}
