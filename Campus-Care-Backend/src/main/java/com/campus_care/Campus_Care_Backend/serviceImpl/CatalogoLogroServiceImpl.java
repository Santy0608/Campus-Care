package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.CatalogoLogros;
import com.campus_care.Campus_Care_Backend.dto.CatalogoLogrosDTO;
import com.campus_care.Campus_Care_Backend.repository.CatalogoLogrosRepository;
import com.campus_care.Campus_Care_Backend.service.CatalogoLogrosService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class CatalogoLogroServiceImpl implements CatalogoLogrosService {

    @Autowired
    private CatalogoLogrosRepository catalogoLogrosRepository;

    @Override
    public List<CatalogoLogrosDTO> listadoCatalogoLogros() {
        List<CatalogoLogros> catalogoLogros = catalogoLogrosRepository.findAll();
        List<CatalogoLogrosDTO> dtos = catalogoLogros
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<CatalogoLogrosDTO> buscarCatalogoLogroPorId(String id) {
        return catalogoLogrosRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public CatalogoLogrosDTO agregarCatalogoLogro(CatalogoLogrosDTO catalogoLogrosDTO) {
        CatalogoLogros catalogoLogros = new CatalogoLogros();
        catalogoLogros.setTitulo(catalogoLogrosDTO.getTitulo());
        catalogoLogros.setDescripcion(catalogoLogrosDTO.getDescripcion());
        catalogoLogros.setCriterioRacha(catalogoLogrosDTO.getCriterioRacha());
        catalogoLogros.setPuntosOtorgados(catalogoLogrosDTO.getPuntosOtorgados());
        catalogoLogros.setUrlIcono(catalogoLogrosDTO.getUrlIcono());
        CatalogoLogros catalogoLogrosAgregado = catalogoLogrosRepository.save(catalogoLogros);
        return convertirADTO(catalogoLogrosAgregado);
    }

    @Override
    public CatalogoLogrosDTO actualizarCatalogoLogro(CatalogoLogrosDTO catalogoLogrosDTO, String id) {
        return catalogoLogrosRepository.findById(id)
                .map(catalogoLogros -> {
                    catalogoLogros.setTitulo(catalogoLogrosDTO.getTitulo());
                    catalogoLogros.setDescripcion(catalogoLogrosDTO.getDescripcion());
                    catalogoLogros.setCriterioRacha(catalogoLogrosDTO.getCriterioRacha());
                    catalogoLogros.setPuntosOtorgados(catalogoLogrosDTO.getPuntosOtorgados());
                    catalogoLogros.setUrlIcono(catalogoLogrosDTO.getUrlIcono());
                    CatalogoLogros catalogoLogrosActualizado = catalogoLogrosRepository.save(catalogoLogros);
                    return convertirADTO(catalogoLogrosActualizado);
                })
                .orElseThrow(() -> new RuntimeException("Catalogo Logro no encontrado"));
    }

    @Override
    public void eliminarCatalogoLogroPorId(String id) {
        catalogoLogrosRepository.deleteById(id);
    }

    CatalogoLogrosDTO convertirADTO(CatalogoLogros catalogoLogros){
        CatalogoLogrosDTO dto = new CatalogoLogrosDTO();
        dto.setId(catalogoLogros.getId());
        dto.setTitulo(catalogoLogros.getTitulo());
        dto.setDescripcion(catalogoLogros.getDescripcion());
        dto.setCriterioRacha(catalogoLogros.getCriterioRacha());
        dto.setPuntosOtorgados(catalogoLogros.getPuntosOtorgados());
        dto.setUrlIcono(catalogoLogros.getUrlIcono());
        return dto;
    }


}
