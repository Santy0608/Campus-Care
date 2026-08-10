package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.Recursos;
import org.bson.Document;
import org.springframework.data.mongodb.core.MongoTemplate;
import org.springframework.data.mongodb.core.aggregation.Aggregation;
import org.springframework.stereotype.Repository;

import java.util.ArrayList;
import java.util.List;

@Repository
public class RecursoVectorRepository {

    private final MongoTemplate mongoTemplate;

    public RecursoVectorRepository(MongoTemplate mongoTemplate){
        this.mongoTemplate = mongoTemplate;
    }

    public List<Recursos> buscarSimilares(float[] queryEmbedding, int limit){
        Document vectorSearchStage = new Document("$vectorSearch", new Document()
                .append("index", "vector_index")
                .append("path", "embedding")
                .append("queryVector", toDoubleList(queryEmbedding))
                .append("numCandidates", limit * 10L)
                .append("limit", (long) limit)
                .append("filter", new Document("activo", true))
        );

        Document scoreStage = new Document("$addFields",
                new Document("score", new Document("$meta", "vectorSearchScore")));

        Aggregation aggregation = Aggregation.newAggregation(
                ctx -> vectorSearchStage,
                ctx -> scoreStage
        );

        return mongoTemplate.aggregate(aggregation, "recursos", Recursos.class).getMappedResults();
    }



    private List<Double> toDoubleList(float[] arr){
        List<Double> list = new ArrayList<>();
        for (float f : arr) {
            list.add((double) f);
        }
        return list;
    }

}
