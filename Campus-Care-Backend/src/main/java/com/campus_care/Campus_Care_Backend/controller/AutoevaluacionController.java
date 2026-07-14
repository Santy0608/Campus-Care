package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.AutoevaluacionDTO;
import com.campus_care.Campus_Care_Backend.service.AutoevaluacionService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/autoevaluaciones")
public class AutoevaluacionController {

    @Autowired
    private AutoevaluacionService autoevaluacionService;

    @PostMapping("/guardar-autoevaluacion")
    public ResponseEntity<?> guardarAutoevaluacion(@RequestBody AutoevaluacionDTO autoevaluacionDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(autoevaluacionService.guardarAutoevaluacion(autoevaluacionDTO));
    }

    @GetMapping("/hoy")
    public ResponseEntity<?> obtenerEvaluacionHoy(@RequestParam String usuarioId) {
        Optional<AutoevaluacionDTO> evaluacion = autoevaluacionService.obtenerEvaluacionHoy(usuarioId);
        return evaluacion
                .map(ResponseEntity::ok)
                .orElse(ResponseEntity.noContent().build());
    }


}
