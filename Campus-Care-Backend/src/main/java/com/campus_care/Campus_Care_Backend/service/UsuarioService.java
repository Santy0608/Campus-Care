package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.domain.Usuario;
import com.campus_care.Campus_Care_Backend.dto.UsuarioDTO;
import org.springframework.web.bind.annotation.PathVariable;

import java.util.List;
import java.util.Optional;

public interface UsuarioService {

    List<UsuarioDTO> listadoUsuarios();

    Optional<UsuarioDTO> buscarUsuarioPorId(String id);

    UsuarioDTO guardarUsuario(UsuarioDTO usuarioDTO);

    UsuarioDTO actualizarUsuario(UsuarioDTO usuarioDTO, String id);

    void eliminarUsuarioPorId(String id);

}
