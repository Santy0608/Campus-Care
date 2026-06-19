package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.DiarioDTO;
import com.campus_care.Campus_Care_Backend.service.DiarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/diarios")
public class DiarioController {

    @Autowired
    private DiarioService diarioService;

    @GetMapping("/listado-diarios")
    public List<DiarioDTO> listadoDiarios(){
        return diarioService.listadoDiarios();
    }

    @GetMapping("/{id}")
    public ResponseEntity<DiarioDTO> buscarDiarioPorId(@PathVariable String id){
        Optional<DiarioDTO> diarioOptional = diarioService.buscarDiarioPorId(id);
        if (diarioOptional.isPresent()){
            return ResponseEntity.ok(diarioOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-diario")
    public ResponseEntity<?> agregarDiario(@RequestBody DiarioDTO diarioDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(diarioService.agregarDiario(diarioDTO));
    }

}
