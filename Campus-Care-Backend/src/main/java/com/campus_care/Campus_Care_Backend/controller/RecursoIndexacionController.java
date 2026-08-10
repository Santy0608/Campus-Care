package com.campus_care.Campus_Care_Backend.controller;

import com.campus_care.Campus_Care_Backend.domain.Recursos;
import com.campus_care.Campus_Care_Backend.service.EmbeddingService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.data.mongodb.core.MongoTemplate;
import org.springframework.data.mongodb.core.query.Criteria;
import org.springframework.data.mongodb.core.query.Query;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;
import java.util.Map;

@RestController
@CrossOrigin(origins = "http://")
@RequestMapping("/api/admin/recursos")
public class RecursoIndexacionController {

    @Autowired
    private MongoTemplate mongoTemplate;

    @Autowired
    private  EmbeddingService embeddingService;

    @PostMapping("/reindexar")
    public ResponseEntity<Map<String, Object>> reindexarTodos() {
        List<Recursos> recursos = mongoTemplate.find(
                Query.query(Criteria.where("embedding").exists(false)),
                Recursos.class
        );

        int exitosos = 0;
        int fallidos = 0;

        for (Recursos recurso : recursos) {
            try {
                String texto = recurso.getTitulo() + ". " + recurso.getContenido();
                float[] embedding = embeddingService.generarEmbedding(texto);
                recurso.setEmbedding(embedding);
                mongoTemplate.save(recurso);
                exitosos++;
            } catch (Exception e) {
                fallidos++;
            }
        }

        return ResponseEntity.ok(Map.of(
                "total", recursos.size(),
                "exitosos", exitosos,
                "fallidos", fallidos
        ));
    }



}
