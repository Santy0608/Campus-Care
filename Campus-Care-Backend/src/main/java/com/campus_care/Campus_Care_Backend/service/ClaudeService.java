package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.serviceImpl.ClaudeServiceImpl;

import java.util.List;

public interface ClaudeService {

    List<ClaudeServiceImpl.RecomendacionInterna> refinarRecomendacion(Autoevaluacion autoevaluacion, List<Recursos> candidatos);

    record RecomendacionInterna(Recursos recurso, String razon) {}


}
