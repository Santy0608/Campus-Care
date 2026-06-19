package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Usuario;
import com.campus_care.Campus_Care_Backend.dto.UsuarioDTO;
import com.campus_care.Campus_Care_Backend.repository.UsuarioRepository;
import com.campus_care.Campus_Care_Backend.service.UsuarioService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class UsuarioServiceImpl implements UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    @Override
    public List<UsuarioDTO> listadoUsuarios() {
        List<Usuario> usuarios = usuarioRepository.findAll();
        List<UsuarioDTO> dtos = usuarios.stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<UsuarioDTO> buscarUsuarioPorId(String id) {
        return usuarioRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public UsuarioDTO guardarUsuario(UsuarioDTO usuarioDTO) {
        Usuario usuario = new Usuario();
        usuario.setNombre(usuarioDTO.getNombre());
        usuario.setApellido(usuarioDTO.getApellido());
        usuario.setEmail(usuarioDTO.getEmail());
        usuario.setNombreUsuario(usuarioDTO.getNombreUsuario());
        usuario.setContrasenia(usuarioDTO.getContrasenia());
        usuario.setRoles(usuarioDTO.getRoles());
        usuario.setPuntosTotales(usuarioDTO.getPuntosTotales());
        usuario.setLogrosObtenidos(usuarioDTO.getLogrosObtenidos());
        Usuario usuarioAgregado = usuarioRepository.save(usuario);
        return convertirADTO(usuarioAgregado);
    }

    @Override
    public UsuarioDTO actualizarUsuario(UsuarioDTO usuarioDTO, String id) {
        return usuarioRepository.findById(id).map(usuario -> {
            usuario.setNombre(usuarioDTO.getNombre());
            usuario.setApellido(usuarioDTO.getApellido());
            usuario.setEmail(usuarioDTO.getEmail());
            usuario.setNombreUsuario(usuarioDTO.getNombreUsuario());
            usuario.setContrasenia(usuarioDTO.getContrasenia());
            usuario.setRoles(usuarioDTO.getRoles());
            usuario.setPuntosTotales(usuarioDTO.getPuntosTotales());
            usuario.setLogrosObtenidos(usuarioDTO.getLogrosObtenidos());
            Usuario usuarioActualizado = usuarioRepository.save(usuario);
            return convertirADTO(usuarioActualizado);
        })
                .orElseThrow(() -> new RuntimeException("Usuario no encontrado " + id));
    }

    @Override
    public void eliminarUsuarioPorId(String id) {
        usuarioRepository.deleteById(id);
    }

    UsuarioDTO  convertirADTO(Usuario usuario){
        UsuarioDTO dto = new UsuarioDTO();
        dto.setNombre(usuario.getNombre());
        dto.setApellido(usuario.getApellido());
        dto.setEmail(usuario.getEmail());
        dto.setNombreUsuario(usuario.getNombreUsuario());
        dto.setContrasenia(usuario.getContrasenia());
        dto.setRoles(usuario.getRoles());
        dto.setPuntosTotales(usuario.getPuntosTotales());
        dto.setLogrosObtenidos(usuario.getLogrosObtenidos());
        return dto;
    }

}

