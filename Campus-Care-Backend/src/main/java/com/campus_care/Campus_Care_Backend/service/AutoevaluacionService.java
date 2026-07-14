package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.dto.AutoevaluacionDTO;

import java.util.Optional;

public interface AutoevaluacionService {

    public AutoevaluacionDTO guardarAutoevaluacion(AutoevaluacionDTO autoevaluacionDTO);

    Optional<AutoevaluacionDTO> obtenerEvaluacionHoy(String usuarioId);

    int calcularRacha(String idUsaurio);

}
