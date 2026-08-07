package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.Diario;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

import java.util.List;

@EnableMongoRepositories
public interface DiarioRepository extends MongoRepository<Diario, String> {

    List<Diario> findByIdUsuario(String idUsuario);


}
