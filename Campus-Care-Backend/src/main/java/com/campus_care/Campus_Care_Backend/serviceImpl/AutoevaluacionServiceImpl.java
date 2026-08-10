package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.*;
import com.campus_care.Campus_Care_Backend.dto.AutoevaluacionDTO;
import com.campus_care.Campus_Care_Backend.dto.RespuestaDTO;
import com.campus_care.Campus_Care_Backend.repository.AutoevaluacionRepository;
import com.campus_care.Campus_Care_Backend.repository.CatalogoLogrosRepository;
import com.campus_care.Campus_Care_Backend.repository.HistorialPuntosRepository;
import com.campus_care.Campus_Care_Backend.repository.LogroUsuarioRepository;
import com.campus_care.Campus_Care_Backend.service.AutoevaluacionService;
import lombok.RequiredArgsConstructor;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.stereotype.Service;
import org.springframework.web.server.ResponseStatusException;
import org.yaml.snakeyaml.events.Event;

import java.time.Instant;
import java.time.LocalDate;
import java.time.ZoneId;
import java.time.ZoneOffset;
import java.time.temporal.ChronoUnit;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;


@Service
@RequiredArgsConstructor
public class AutoevaluacionServiceImpl implements AutoevaluacionService {

    @Autowired
    private AutoevaluacionRepository autoevaluacionRepository;

    @Autowired
    private HistorialPuntosRepository historialPuntosRepository;

    @Autowired
    private LogroUsuarioRepository logroUsuarioRepository;

    @Autowired
    private CatalogoLogrosRepository catalogoLogrosRepository;

    @Override
    public AutoevaluacionDTO guardarAutoevaluacion(AutoevaluacionDTO autoevaluacionDTO) {
        String idUsuario = autoevaluacionDTO.getIdUsuario();

        Instant inicioDelDia = LocalDate.now(ZoneOffset.UTC)
                .atStartOfDay(ZoneOffset.UTC).toInstant();
        Instant finDelDia = inicioDelDia.plus(1, ChronoUnit.DAYS);

        boolean yaEvaluoHoy = autoevaluacionRepository
                .findByIdUsuarioAndFechaEvaluacionBetween(idUsuario, inicioDelDia, finDelDia)
                .isPresent();

        if (yaEvaluoHoy) {
            throw new ResponseStatusException(
                    HttpStatus.BAD_REQUEST,
                    "El usuario ya realizó su autoevaluación hoy. Puede volver mañana."
            );
        }

        Autoevaluacion autoevaluacion = new Autoevaluacion();
        autoevaluacion.setIdUsuario(idUsuario);
        autoevaluacion.setFechaEvaluacion(Instant.now());

        List<Respuesta> listaDatos = new ArrayList<>();
        for (RespuestaDTO respuestaDTO : autoevaluacionDTO.getRespuestas()) {
            Respuesta respuesta = new Respuesta();
            respuesta.setMetrica(respuestaDTO.getMetrica());
            respuesta.setScore(respuestaDTO.getScore());
            listaDatos.add(respuesta);
        }

        autoevaluacion.setRespuestas(listaDatos);
        Autoevaluacion autoevaluacionAgregada = autoevaluacionRepository.save(autoevaluacion);

        int puntosGanados = obtenerPuntosBase();
        HistorialPuntos historial = new HistorialPuntos();
        historial.setIdUsuario(idUsuario);
        historial.setPuntosGanados(puntosGanados);
        historial.setConcepto("AUTOEVALUACION_DIARIA");
        historial.setFecha(Instant.now());
        historialPuntosRepository.save(historial);

        int totalGanadoHoy = puntosGanados;

        int rachaActual = calcularRacha(idUsuario);

        String criterio = "RACHA_" + rachaActual;
        List<CatalogoLogros> logrosDeRacha = catalogoLogrosRepository
                .findByCriterioRacha(criterio);

        for(CatalogoLogros logro: logrosDeRacha){


            LogroUsuario logroUsuario = new LogroUsuario();
            logroUsuario.setIdUsuario(idUsuario);
            logroUsuario.setIdLogro(logro.getId());
            logroUsuario.setFechaDesbloqueo(Instant.now());
            logroUsuarioRepository.save(logroUsuario);

            //Sumar puntos al historial
            HistorialPuntos historialLogro = new HistorialPuntos();
            historialLogro.setIdUsuario(idUsuario);
            historialLogro.setPuntosGanados(logro.getPuntosOtorgados());
            historialLogro.setConcepto("LOGRO_" + logro.getTitulo().toUpperCase().replace(" ", "_"));
            historialLogro.setFecha(Instant.now());
            historialPuntosRepository.save(historialLogro);

            totalGanadoHoy += logro.getPuntosOtorgados();

        }

        AutoevaluacionDTO dto = convertirADTO(autoevaluacionAgregada);
        dto.setPuntosGanados(totalGanadoHoy);
        return dto;

    }

    @Override
    public Optional<AutoevaluacionDTO> obtenerEvaluacionHoy(String usuarioId) {
        LocalDate hoy = LocalDate.now(ZoneOffset.UTC);
        Instant inicioDelDia = hoy.atStartOfDay(ZoneOffset.UTC).toInstant();
        Instant finDelDia = hoy.plusDays(1).atStartOfDay(ZoneOffset.UTC).toInstant();

        Optional<Autoevaluacion> evaluacion = autoevaluacionRepository
                .findByIdUsuarioAndFechaEvaluacionBetween(usuarioId, inicioDelDia, finDelDia);

        return evaluacion.map(this::convertirADTO);
    }

    private int obtenerPuntosBase(){
        return catalogoLogrosRepository.findByCriterioRacha("DIARIA")
                .stream()
                .findFirst()
                .map(CatalogoLogros::getPuntosOtorgados)
                .orElse(50); //Fallback Configurable
    }

    @Override
    public int calcularRacha(String idUsuario){
        List<Autoevaluacion> historial = autoevaluacionRepository
                .findByIdUsuarioOrderByFechaEvaluacionDesc(idUsuario);

        if (historial.isEmpty()){
            return 0;
        }

        LocalDate hoy = LocalDate.now(ZoneOffset.UTC);
        LocalDate fechaMasReciente = historial.get(0)
                .getFechaEvaluacion()
                .atZone(ZoneOffset.UTC)
                .toLocalDate();

        if (fechaMasReciente.isBefore(hoy.minusDays(1))) {
            return 0;
        }

        int racha = 1;
        LocalDate fechaEsperada = fechaMasReciente.minusDays(1);

        for (int i = 1; i < historial.size(); i++) {
            LocalDate fechaEvaluacion = historial.get(i)
                    .getFechaEvaluacion()
                    .atZone(ZoneOffset.UTC)
                    .toLocalDate();

            if (fechaEvaluacion.equals(fechaEsperada)) {
                racha++;
                fechaEsperada = fechaEsperada.minusDays(1);
            } else {
                break;
            }
        }
        return racha;
    }


    AutoevaluacionDTO convertirADTO(Autoevaluacion autoevaluacion){
        AutoevaluacionDTO dto = new AutoevaluacionDTO();
        dto.setId(autoevaluacion.getId());
        dto.setIdUsuario(autoevaluacion.getIdUsuario());
        dto.setFechaEvaluacion(autoevaluacion.getFechaEvaluacion());
        List<RespuestaDTO> listaDtos = new ArrayList<>();
        for (Respuesta resp : autoevaluacion.getRespuestas()) {
            RespuestaDTO respDto = new RespuestaDTO();
            respDto.setMetrica(resp.getMetrica());
            respDto.setScore(resp.getScore());
            listaDtos.add(respDto);
        }
        dto.setRespuestas(listaDtos);
        return dto;
    }

}
