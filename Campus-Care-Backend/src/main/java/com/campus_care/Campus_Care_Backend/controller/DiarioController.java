package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.DiarioDTO;
import com.campus_care.Campus_Care_Backend.service.DiarioService;
import com.campus_care.Campus_Care_Backend.serviceImpl.CustomUserDetails;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/diarios")
public class DiarioController {

    @Autowired
    private DiarioService diarioService;

    @GetMapping("/listado-diarios")
    public List<DiarioDTO> listadoDiarios(){
        return diarioService.listadoDiarios();
    }

    @GetMapping("/mis-diarios")
    public List<DiarioDTO> listadoDiariosDelUsuario(Authentication authentication){
        CustomUserDetails userDetails = (CustomUserDetails) authentication.getPrincipal();
        String idUsuario = userDetails.getId();
        return diarioService.listadoDiarioPorUsuario(idUsuario);
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
