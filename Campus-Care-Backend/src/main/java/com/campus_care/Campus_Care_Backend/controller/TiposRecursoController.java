package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.TiposRecursoDTO;
import com.campus_care.Campus_Care_Backend.service.TipoRecursoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/tipos-recurso")
@RequiredArgsConstructor
public class TiposRecursoController {

    @Autowired
    private TipoRecursoService tipoRecursoService;

    @GetMapping("/listado-tipos-recurso")
    List<TiposRecursoDTO> listadoTiposRecurso(){
        return tipoRecursoService.listadoTiposRecursos();
    }

    @GetMapping("/{id}")
    public ResponseEntity<TiposRecursoDTO> buscarTipoRecursoPorId(@PathVariable String id){
        Optional<TiposRecursoDTO> tipoRecursoOptional = tipoRecursoService.buscarTipoRecursoPorId(id);
        if (tipoRecursoOptional.isPresent()){
            return ResponseEntity.ok(tipoRecursoOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping
    public ResponseEntity<?> guardarTiposRecurso(@RequestBody TiposRecursoDTO tiposRecursoDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(tipoRecursoService.agregarTipoRecurso(tiposRecursoDTO));
    }

    @PutMapping("/actualizar-tipos-recurso/{id}")
    public ResponseEntity<?> actualizarTiposRecurso(@RequestBody TiposRecursoDTO tiposRecursoDTO, @PathVariable String id){
        return ResponseEntity.ok(tipoRecursoService.actualizarTipoRecurso(tiposRecursoDTO, id));
    }

    @DeleteMapping("/eliminar-tipos-recurso/{id}")
    public ResponseEntity<TiposRecursoDTO> eliminarTipoRecurso(@PathVariable String id){
        Optional<TiposRecursoDTO> tiposRecursoOptional = tipoRecursoService.buscarTipoRecursoPorId(id);
        if (tiposRecursoOptional.isPresent()){
            tipoRecursoService.eliminarTipoRecurso(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }


}
