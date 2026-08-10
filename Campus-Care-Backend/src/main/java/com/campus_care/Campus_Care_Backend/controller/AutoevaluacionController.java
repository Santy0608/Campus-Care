package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.dto.AutoevaluacionDTO;
import com.campus_care.Campus_Care_Backend.repository.AutoevaluacionRepository;
import com.campus_care.Campus_Care_Backend.service.AutoevaluacionService;
import com.campus_care.Campus_Care_Backend.service.RecomendadorService;
import com.campus_care.Campus_Care_Backend.serviceImpl.ClaudeServiceImpl;
import com.campus_care.Campus_Care_Backend.serviceImpl.RecomendadorServiceImpl;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/autoevaluaciones")
public class AutoevaluacionController {

    @Autowired
    private AutoevaluacionService autoevaluacionService;

    @Autowired
    private AutoevaluacionRepository autoevaluacionRepository;

    @Autowired
    private RecomendadorService recomendadorService;

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

    @GetMapping("/{id}/recomendaciones")
    public ResponseEntity<List<RecomendadorService.RecursoRecomendado>> obtenerRecomendacion(@PathVariable String id){
        Autoevaluacion autoevaluacion = autoevaluacionRepository.findById(id)
                .orElseThrow(() -> new RuntimeException("Autoevaluación no encontrada"));
        return ResponseEntity.ok(recomendadorService.recomendar(autoevaluacion));
    }


}
