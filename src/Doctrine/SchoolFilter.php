<?php

namespace App\Doctrine;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class SchoolFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        // Check if the entity is "School Aware"
        // (We check if it has a 'school' association mapping)
        if (!$targetEntity->hasAssociation('school')) {
            return '';
        }

        // We assume the column name is 'school_id' which is standard Doctrine naming
        // But to be safe, valid implementations usually check the association mapping.
        // For simplicity/robustness in this context:
        
        try {
            // Get the column name for the 'school' association
            $association = $targetEntity->getAssociationMapping('school');
            $columnName = $association['joinColumns'][0]['name']; 
            
            return sprintf('%s.%s = %s', $targetTableAlias, $columnName, $this->getParameter('school_id'));
        } catch (\Exception $e) {
            // If mapping is complex or missing, skip
            return '';
        }
    }
}
