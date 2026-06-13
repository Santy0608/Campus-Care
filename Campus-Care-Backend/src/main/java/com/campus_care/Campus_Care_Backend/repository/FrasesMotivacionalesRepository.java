package com.campus_care.Campus_Care_Backend.repository;

import com.campus_care.Campus_Care_Backend.domain.FrasesMotivacionales;
import com.campus_care.Campus_Care_Backend.dto.FrasesMotivacionalesDTO;
import org.springframework.data.mongodb.repository.MongoRepository;
import org.springframework.data.mongodb.repository.config.EnableMongoRepositories;

@EnableMongoRepositories
public interface FrasesMotivacionalesRepository extends MongoRepository<FrasesMotivacionales, String> {



}
