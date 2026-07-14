package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Categoria;
import com.campus_care.Campus_Care_Backend.domain.FrasesMotivacionales;
import com.campus_care.Campus_Care_Backend.dto.FrasesMotivacionalesDTO;
import com.campus_care.Campus_Care_Backend.repository.FrasesMotivacionalesRepository;
import com.campus_care.Campus_Care_Backend.service.FrasesMotivacionalesService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.List;
import java.util.Optional;
import java.util.stream.Collectors;

@Service
@RequiredArgsConstructor
public class FraseMotivacionalServiceImpl implements FrasesMotivacionalesService {

    @Autowired
    private FrasesMotivacionalesRepository frasesMotivacionalesRepository;

    @Override
    public List<FrasesMotivacionalesDTO> listadoFrasesMotivacionales() {
        List<FrasesMotivacionales> frasesMotivacionales = frasesMotivacionalesRepository.findAll();
        List<FrasesMotivacionalesDTO> dtos = frasesMotivacionales
                .stream()
                .map(this::convertirADTO)
                .collect(Collectors.toList());
        return dtos;
    }

    @Override
    public Optional<FrasesMotivacionalesDTO> buscarFraseMotivacionalPorId(String id) {
        return frasesMotivacionalesRepository.findById(id).map(this::convertirADTO);
    }

    @Override
    public FrasesMotivacionalesDTO guardarFraseMotivacional(FrasesMotivacionalesDTO frasesMotivacionalesDTO) {
        FrasesMotivacionales frasesMotivacionales = new FrasesMotivacionales();
        frasesMotivacionales.setTexto(frasesMotivacionalesDTO.getTexto());
        frasesMotivacionales.setAutor(frasesMotivacionalesDTO.getAutor());
        frasesMotivacionales.setActivo(true);
        FrasesMotivacionales fraseMotivacionalAgregada = frasesMotivacionalesRepository.save(frasesMotivacionales);
        return convertirADTO(fraseMotivacionalAgregada);
    }

    @Override
    public FrasesMotivacionalesDTO actualizarFraseMotivacional(FrasesMotivacionalesDTO frasesMotivacionalesDTO, String id) {
        return frasesMotivacionalesRepository.findById(id)
                .map(frasesMotivacionales -> {
                    frasesMotivacionales.setId(frasesMotivacionalesDTO.getId());
                    frasesMotivacionales.setTexto(frasesMotivacionalesDTO.getTexto());
                    frasesMotivacionales.setAutor(frasesMotivacionalesDTO.getAutor());
                    frasesMotivacionales.setActivo(true);
                    FrasesMotivacionales fraseMotivacionalActualizada = frasesMotivacionalesRepository.save(frasesMotivacionales);
                    return convertirADTO(fraseMotivacionalActualizada);
                })
                .orElseThrow(() -> new RuntimeException("Frase Motivacional no encontrada con ID: " + id));

    }

    @Override
    public void eliminarFraseMotivacionalPorId(String id) {
        frasesMotivacionalesRepository.deleteById(id);
    }

    FrasesMotivacionalesDTO convertirADTO(FrasesMotivacionales frasesMotivacionales){
        FrasesMotivacionalesDTO dto = new FrasesMotivacionalesDTO();
        dto.setId(frasesMotivacionales.getId());
        dto.setTexto(frasesMotivacionales.getTexto());
        dto.setAutor(frasesMotivacionales.getAutor());
        dto.setActivo(frasesMotivacionales.isActivo());
        return dto;
    }

}
