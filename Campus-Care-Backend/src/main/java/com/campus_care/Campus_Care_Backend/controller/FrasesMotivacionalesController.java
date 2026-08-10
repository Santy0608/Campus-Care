package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.FrasesMotivacionales;
import com.campus_care.Campus_Care_Backend.dto.FrasesMotivacionalesDTO;
import com.campus_care.Campus_Care_Backend.service.FrasesMotivacionalesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://127.0.0.1:5500")
@RequestMapping("/api/frases-motivacionales")
public class FrasesMotivacionalesController {

    @Autowired
    private FrasesMotivacionalesService frasesMotivacionalesService;

    @GetMapping("/listado-frases-motivacionales")
    public List<FrasesMotivacionalesDTO> listadoFrasesMotivacionales(){
        return frasesMotivacionalesService.listadoFrasesMotivacionales();
    }

    @GetMapping("/{id}")
    public ResponseEntity<FrasesMotivacionalesDTO> buscarFraseMotivacionalPorId(@PathVariable String id){
        Optional<FrasesMotivacionalesDTO> frasesMotivacionalesOptional = frasesMotivacionalesService.buscarFraseMotivacionalPorId(id);
        if (frasesMotivacionalesOptional.isPresent()){
            return ResponseEntity.ok(frasesMotivacionalesOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-frase-motivacional")
    public ResponseEntity<?> guardarFraseMotivacionalPorId(@RequestBody FrasesMotivacionalesDTO frasesMotivacionales){
        return ResponseEntity.status(HttpStatus.CREATED).body(frasesMotivacionalesService.guardarFraseMotivacional(frasesMotivacionales));
    }

    @PutMapping("/actualizar-frase-motivacional/{id}")
    public ResponseEntity<?> actualizarFraseMotivacional(@RequestBody FrasesMotivacionalesDTO frasesMotivacionales, @PathVariable String id){
        return ResponseEntity.ok(frasesMotivacionalesService.actualizarFraseMotivacional(frasesMotivacionales, id));
    }

    @DeleteMapping("/eliminar-frase-motivacional/{id}")
    public ResponseEntity<FrasesMotivacionalesDTO> eliminarFraseMotivacionalPorId(@PathVariable String id){
        Optional<FrasesMotivacionalesDTO> fraseMotivacionalOptional = frasesMotivacionalesService.buscarFraseMotivacionalPorId(id);
        if (fraseMotivacionalOptional.isPresent()){
            frasesMotivacionalesService.eliminarFraseMotivacionalPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }

}
