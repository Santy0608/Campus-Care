package com.campus_care.Campus_Care_Backend.serviceImpl;

import com.campus_care.Campus_Care_Backend.service.EmbeddingService;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.data.mongodb.core.mapping.Field;
import org.springframework.http.MediaType;
import org.springframework.stereotype.Service;
import org.springframework.web.client.RestClient;

import java.util.List;
import java.util.Map;

@Service
public class EmbeddingServiceImpl implements EmbeddingService {

    @Value("${voyage.api.key}")
    private String voyageApiKey;

    private final RestClient restClient = RestClient.create("https://api.voyageai.com/v1");

    @Override
    public float[] generarEmbedding(String texto) {
        Map<String, Object> body = Map.of(
                "input", List.of(texto),
                "model", "voyage-3-lite"
        );

        var response = restClient.post()
                .uri("/embeddings")
                .header("Authorization", "Bearer " + voyageApiKey)
                .contentType(MediaType.APPLICATION_JSON)
                .body(body)
                .retrieve()
                .body(VoyageEmbeddingResponse.class);

        return response.data().get(0).embedding();
    }

}

record VoyageEmbeddingResponse(List<EmbeddingData> data) {}
record EmbeddingData(float[] embedding) {}
