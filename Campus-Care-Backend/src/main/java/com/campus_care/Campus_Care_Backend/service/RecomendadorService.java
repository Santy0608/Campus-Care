package com.campus_care.Campus_Care_Backend.service;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.dto.RecursosDTO;
import com.campus_care.Campus_Care_Backend.serviceImpl.ClaudeServiceImpl;
import com.campus_care.Campus_Care_Backend.serviceImpl.RecomendadorServiceImpl;

import java.util.List;

public interface RecomendadorService {

    List<RecomendadorServiceImpl.RecursoRecomendado> recomendar(Autoevaluacion autoevaluacion);

    record RecursoRecomendado(RecursosDTO recurso, String razon) {}


}
