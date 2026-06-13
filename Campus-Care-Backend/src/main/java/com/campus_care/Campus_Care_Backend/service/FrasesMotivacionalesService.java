package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.FrasesMotivacionalesDTO;

import java.util.List;
import java.util.Optional;

public interface FrasesMotivacionalesService {

    List<FrasesMotivacionalesDTO> listadoFrasesMotivacionales();

    Optional<FrasesMotivacionalesDTO> buscarFraseMotivacionalPorId(String id);

    FrasesMotivacionalesDTO guardarFraseMotivacional(FrasesMotivacionalesDTO frasesMotivacionalesDTO);

    FrasesMotivacionalesDTO actualizarFraseMotivacional(FrasesMotivacionalesDTO frasesMotivacionalesDTO, String id);

    void eliminarFraseMotivacionalPorId(String id);


}
