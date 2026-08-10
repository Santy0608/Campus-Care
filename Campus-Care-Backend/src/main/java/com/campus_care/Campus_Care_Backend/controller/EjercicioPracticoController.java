package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.EjercicioPracticoDTO;
import com.campus_care.Campus_Care_Backend.repository.EjercicioPracticoRepository;
import com.campus_care.Campus_Care_Backend.service.EjercicioPracticoService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.mongodb.core.MongoTemplate;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;
import java.util.OptionalInt;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/ejercicios-practicos")
public class EjercicioPracticoController {

    @Autowired
    private MongoTemplate mongoTemplate;

    @GetMapping("/test-db-name")
    public String testDbName() {
        return mongoTemplate.getDb().getName();
    }

    @Autowired
    private EjercicioPracticoService ejercicioPracticoService;

    @Autowired
    private EjercicioPracticoRepository ejercicioPracticoRepository;

    @GetMapping("/listado-ejercicios-practicos")
    public List<EjercicioPracticoDTO> listadoEjerciciosPracticos(){
        return ejercicioPracticoService.listadoEjerciciosPracticos();
    }

    @GetMapping("/test-count")
    public long testCount() {
        return ejercicioPracticoRepository.count();
    }

    @GetMapping("/{id}")
    public ResponseEntity<EjercicioPracticoDTO> buscarEjercicioPracticoPorId(@PathVariable String id){
        Optional<EjercicioPracticoDTO> ejercicioPracticoOptional = ejercicioPracticoService.buscarEjercicioPracticoPorId(id);
        if (ejercicioPracticoOptional.isPresent()){
            return ResponseEntity.ok(ejercicioPracticoOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-ejercicio-practico")
    public ResponseEntity<?> agregarEjercicioPractico(@RequestBody EjercicioPracticoDTO ejercicioPracticoDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(ejercicioPracticoService.guardarEjercicioPractico(ejercicioPracticoDTO));
    }

    @PutMapping("/actualizar-ejercicio-practico/{id}")
    public ResponseEntity<?> actualizarEjercicioPractico(@RequestBody EjercicioPracticoDTO ejercicioPracticoDTO, @PathVariable String id){
        return ResponseEntity.ok(ejercicioPracticoService.actualizarEjercicioPractico(ejercicioPracticoDTO, id));
    }

    @DeleteMapping("/eliminar-ejercicio-practico/{id}")
    public ResponseEntity<?> eliminarEjercicioPractico(@PathVariable String id){
        Optional<EjercicioPracticoDTO> ejercicioPracticoOptional = ejercicioPracticoService.buscarEjercicioPracticoPorId(id);
        if (ejercicioPracticoOptional.isPresent()){
            ejercicioPracticoService.eliminarEjercicioPractico(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }




}
