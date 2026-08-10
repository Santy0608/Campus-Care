package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.domain.Autoevaluacion;
import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.service.ClaudeService;
import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.core.type.TypeReference;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestClient;

import java.util.*;
import java.util.stream.Collectors;

@Service
public class ClaudeServiceImpl implements ClaudeService {

    @Value("${anthropic.api.key}")
    private String apiKey;

    private final RestClient restClient = RestClient.create("https://api.anthropic.com/v1");
    private final ObjectMapper objectMapper = new ObjectMapper();

    private static final Logger log = LoggerFactory.getLogger(ClaudeService.class);

    @Override
    public List<RecomendacionInterna> refinarRecomendacion(Autoevaluacion autoevaluacion, List<Recursos> candidatos, String contextoDiario) {
        String prompt  = construirPrompt(autoevaluacion, candidatos, contextoDiario);

        Map<String, Object> body = Map.of(
                "model", "claude-sonnet-4-6",
                "max_tokens", 1024,
                "messages", List.of(Map.of("role", "user", "content", prompt))
        );

        try{
            Map<String, Object> response = restClient.post()
                    .uri("/messages")
                    .header("x-api-key", apiKey)
                    .header("anthropic-version", "2023-06-01")
                    .header("Content-Type", "application/json")
                    .body(body)
                    .retrieve()
                    .body(Map.class);

            String textoRespuesta = extraerTexto(response);
            List<RecomendacionClaude> orden = parsearRespuesta(textoRespuesta);
            return combinarConRazon(candidatos, orden);
        } catch (Exception e){
            log.error("Fallo al refinar recomendación con Claude, usando fallback de vector search", e);
            return fallbackSinRazon(candidatos);
        }

    }




    private String truncar(String texto, int max) {
        return texto.length() <= max ? texto : texto.substring(0, max) + "...";
    }

    @SuppressWarnings("unchecked")
    private String extraerTexto(Map<String, Object> response) {
        List<Map<String, Object>> content = (List<Map<String, Object>>) response.get("content");
        return content.stream()
                .filter(block -> "text".equals(block.get("type")))
                .map(block -> (String) block.get("text"))
                .findFirst()
                .orElseThrow(() -> new RuntimeException("Claude no devolvió texto"));
    }

    private String construirPrompt(Autoevaluacion autoevaluacion, List<Recursos> candidatos, String contextoDiario) {
        String metricas = autoevaluacion.getRespuestas().stream()
                .map(r -> r.getMetrica() + ": " + r.getScore() + "/5")
                .collect(Collectors.joining(", "));

        String recursosTexto = candidatos.stream()
                .map(r -> String.format("id: %s | título: %s | contenido: %s",
                        r.getId(), r.getTitulo(), truncar(r.getContenido(), 200)))
                .collect(Collectors.joining("\n"));

        String bloqueDiario = contextoDiario == null || contextoDiario.isBlank()
                ? ""
                : "\nEl estudiante también escribió esto en su diario recientemente: \"" + truncar(contextoDiario, 300) + "\"\n";

        return """
        Eres un asistente de bienestar estudiantil. Un estudiante completó esta autoevaluación (escala 1-5, 1=malo, 5=bueno):
        %s
        %s
        Estos son los recursos candidatos disponibles:
        %s

        Selecciona y ordena hasta 3 recursos genuinamente relevantes para este estudiante según su estado actual.
        Nunca repitas el mismo id más de una vez. Si hay menos de 3 recursos realmente relevantes, devuelve solo esos.
        Si usaste el diario para justificar una recomendación, menciónalo brevemente en la razón.
        Responde ÚNICAMENTE con un JSON válido, sin texto adicional, sin markdown, en este formato exacto:
        [{"id": "...", "razon": "..."}]
        """.formatted(metricas, bloqueDiario, recursosTexto);
    }

    private List<RecomendacionClaude> parsearRespuesta(String texto) throws JsonProcessingException {
        String limpio = texto.replaceAll("```json|```", "").trim();
        return objectMapper.readValue(limpio, new TypeReference<List<RecomendacionClaude>>() {});
    }

    private List<RecomendacionInterna> combinarConRazon(List<Recursos> candidatos, List<RecomendacionClaude> orden) {
        Map<String, Recursos> porId = candidatos.stream()
                .collect(Collectors.toMap(Recursos::getId, r -> r));

        Set<String> idsVistos = new HashSet<>();

        return orden.stream()
                .filter(rec -> idsVistos.add(rec.id())) // false si el id ya estaba, lo descarta
                .map(rec -> {
                    Recursos recurso = porId.get(rec.id());
                    return recurso != null ? new RecomendacionInterna(recurso, rec.razon()) : null;
                })
                .filter(Objects::nonNull)
                .collect(Collectors.toList());
    }

    private List<RecomendacionInterna> fallbackSinRazon(List<Recursos> candidatos) {
        return candidatos.stream()
                .map(r -> new RecomendacionInterna(r, "Recomendado según similitud con tu autoevaluación"))
                .collect(Collectors.toList());
    }


    public record RecomendacionClaude(String id, String razon) {}

}


