package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.dto.CatalogoLogrosDTO;
import com.campus_care.Campus_Care_Backend.service.CatalogoLogrosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import javax.swing.text.html.Option;
import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/catalogos-logros")
public class CatalogoLogroController {

    @Autowired
    private CatalogoLogrosService catalogoLogrosService;

    @GetMapping("/listado-catalogos-logros")
    public List<CatalogoLogrosDTO> listadoCatalogosLogros(){
        return catalogoLogrosService.listadoCatalogoLogros();
    }

    @GetMapping("/{id}")
    public ResponseEntity<CatalogoLogrosDTO> buscarCatalogoLogroPorId(@PathVariable String id){
        Optional<CatalogoLogrosDTO> catalogoLogroOptional = catalogoLogrosService.buscarCatalogoLogroPorId(id);
        if (catalogoLogroOptional.isPresent()){
            return ResponseEntity.ok(catalogoLogroOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-catalogo-logro")
    public ResponseEntity<?> agregarCatalogoLogro(@RequestBody CatalogoLogrosDTO catalogoLogrosDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(catalogoLogrosService.agregarCatalogoLogro(catalogoLogrosDTO));
    }

    @PutMapping("/actualizar-catalogo-logro/{id}")
    public ResponseEntity<?> actualizarCatalogoLogro(@RequestBody CatalogoLogrosDTO catalogoLogrosDTO, @PathVariable String id){
        return ResponseEntity.ok(catalogoLogrosService.actualizarCatalogoLogro(catalogoLogrosDTO, id));
    }

    @DeleteMapping("/eliminar-catalogo-logro/{id}")
    public ResponseEntity<CatalogoLogrosDTO> eliminarCatalogoLogroPorId(@PathVariable String id){
        Optional<CatalogoLogrosDTO> catalogoLogroOptional = catalogoLogrosService.buscarCatalogoLogroPorId(id);
        if (catalogoLogroOptional.isPresent()){
            catalogoLogrosService.eliminarCatalogoLogroPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }

}
