package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.LineasApoyoDTO;
import com.campus_care.Campus_Care_Backend.service.LineasApoyoService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/lineas-apoyo")
public class LineasApoyoController {

    @Autowired
    private LineasApoyoService lineasApoyoService;

    @GetMapping("/listado-lineas-apoyo")
    public List<LineasApoyoDTO> listadoLineasApoyo(){
        return lineasApoyoService.listadoLineasApoyo();
    }

    @GetMapping("/{id}")
    public ResponseEntity<LineasApoyoDTO> buscarLineaApoyoPorId(@PathVariable String id){
        Optional<LineasApoyoDTO> lineasApoyoOptional = lineasApoyoService.buscarLineaApoyoPorId(id);
        if (lineasApoyoOptional.isPresent()){
            return ResponseEntity.ok(lineasApoyoOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-linea-apoyo")
    public ResponseEntity<?> agregarLineaApoyo(@RequestBody LineasApoyoDTO lineasApoyoDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(lineasApoyoService.agregarLineaApoyo(lineasApoyoDTO));
    }

    @PutMapping("/actualizar-linea-apoyo/{id}")
    public ResponseEntity<?> actualizarLineaApoyo(@RequestBody LineasApoyoDTO lineasApoyoDTO, @PathVariable String id){
        return ResponseEntity.ok(lineasApoyoService.actualizarLineaApoyo(lineasApoyoDTO, id));
    }

    @DeleteMapping("/eliminar-linea-apoyo/{id}")
    public ResponseEntity<LineasApoyoDTO> eliminarLineaApoyo(@PathVariable String id){
        Optional<LineasApoyoDTO> lineasApoyoOptional = lineasApoyoService.buscarLineaApoyoPorId(id);
        if (lineasApoyoOptional.isPresent()){
            lineasApoyoService.eliminarPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }

}
