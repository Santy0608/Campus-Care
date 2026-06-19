package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.Usuario;
import com.campus_care.Campus_Care_Backend.dto.UsuarioDTO;
import com.campus_care.Campus_Care_Backend.service.UsuarioService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Optional;

@RestController
@CrossOrigin(origins = "http://localhost:4200")
@RequestMapping("/api/usuarios")
public class UsuarioController {

    @Autowired
    private UsuarioService usuarioService;

    @GetMapping("/listado-usuarios")
    public List<UsuarioDTO> listadoUsuarios(){
        return usuarioService.listadoUsuarios();
    }

    @GetMapping("/{id}")
    public ResponseEntity<UsuarioDTO> buscarUsuarioPorId(@PathVariable String id){
        Optional<UsuarioDTO> usuarioOptional = usuarioService.buscarUsuarioPorId(id);
        if (usuarioOptional.isPresent()){
            return ResponseEntity.ok(usuarioOptional.orElseThrow());
        }
        return ResponseEntity.notFound().build();
    }

    @PostMapping("/agregar-usuario")
    public ResponseEntity<?> agregarUsuario(@RequestBody UsuarioDTO usuarioDTO){
        return ResponseEntity.status(HttpStatus.CREATED).body(usuarioService.guardarUsuario(usuarioDTO));
    }

    @PutMapping("/actualizar-usuario/{id}")
    public ResponseEntity<?> actualizarUsuario(@RequestBody UsuarioDTO usuarioDTO, @PathVariable String id){
        return ResponseEntity.ok(usuarioService.actualizarUsuario(usuarioDTO, id));
    }

    @DeleteMapping("/eliminar-usuario/{id}")
    public ResponseEntity<UsuarioDTO> eliminarUsuarioPorId(@PathVariable String id){
        Optional<UsuarioDTO> usuarioOptional = usuarioService.buscarUsuarioPorId(id);
        if (usuarioOptional.isPresent()){
            usuarioService.eliminarUsuarioPorId(id);
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.notFound().build();
    }


}
