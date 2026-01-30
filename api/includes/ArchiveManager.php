<?php
/**
 * Archive Manager Class
 * Handles archiving deleted records
 */

class ArchiveManager {
    private $db;
    private $deletedBy;
    
    public function __construct($db, $deletedBy = 'System') {
        $this->db = $db;
        $this->deletedBy = $deletedBy;
    }
    
    /**
     * Archive an admission record
     */
    public function archiveAdmission($admissionId, $reason = 'Manual deletion') {
        try {
            // Get the admission record first
            $stmt = $this->db->prepare("SELECT * FROM admissions WHERE id = :id");
            $stmt->bindParam(':id', $admissionId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Admission not found'];
            }
            
            $admission = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert into archive table
            $archiveStmt = $this->db->prepare("
                INSERT INTO archive_admissions (
                    original_id, application_id, first_name, middle_name, last_name,
                    email, phone, birthdate, gender, address, admission_type, program_id,
                    status, student_id, submitted_at, updated_at, deleted_at, deleted_by, delete_reason, notes
                ) VALUES (
                    :original_id, :application_id, :first_name, :middle_name, :last_name,
                    :email, :phone, :birthdate, :gender, :address, :admission_type, :program_id,
                    :status, :student_id, :submitted_at, :updated_at, NOW(), :deleted_by, :delete_reason, :notes
                )
            ");
            
            $archiveStmt->bindParam(':original_id', $admission['id']);
            $archiveStmt->bindParam(':application_id', $admission['application_id']);
            $archiveStmt->bindParam(':first_name', $admission['first_name']);
            $archiveStmt->bindParam(':middle_name', $admission['middle_name']);
            $archiveStmt->bindParam(':last_name', $admission['last_name']);
            $archiveStmt->bindParam(':email', $admission['email']);
            $archiveStmt->bindParam(':phone', $admission['phone']);
            $archiveStmt->bindParam(':birthdate', $admission['birthdate']);
            $archiveStmt->bindParam(':gender', $admission['gender']);
            $archiveStmt->bindParam(':address', $admission['address']);
            $archiveStmt->bindParam(':admission_type', $admission['admission_type']);
            $archiveStmt->bindParam(':program_id', $admission['program_id']);
            $archiveStmt->bindParam(':status', $admission['status']);
            $archiveStmt->bindParam(':student_id', $admission['student_id']);
            $archiveStmt->bindParam(':submitted_at', $admission['submitted_at']);
            $archiveStmt->bindParam(':updated_at', $admission['updated_at']);
            $archiveStmt->bindParam(':deleted_by', $this->deletedBy);
            $archiveStmt->bindParam(':delete_reason', $reason);
            $archiveStmt->bindParam(':notes', $admission['notes']);
            
            $this->db->beginTransaction();
            
            if ($archiveStmt->execute()) {
                // Delete the original record
                $deleteStmt = $this->db->prepare("DELETE FROM admissions WHERE id = :id");
                $deleteStmt->bindParam(':id', $admissionId);
                
                if ($deleteStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Admission archived successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to delete original admission'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to archive admission'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Restore an archived admission
     */
    public function restoreAdmission($archiveId) {
        try {
            // Get the archived record
            $stmt = $this->db->prepare("SELECT * FROM archive_admissions WHERE id = :id");
            $stmt->bindParam(':id', $archiveId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Archived admission not found'];
            }
            
            $archived = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if original ID already exists
            $checkStmt = $this->db->prepare("SELECT id FROM admissions WHERE id = :id");
            $checkStmt->bindParam(':id', $archived['original_id']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Original admission already exists'];
            }
            
            // Restore to original table
            $restoreStmt = $this->db->prepare("
                INSERT INTO admissions (
                    id, application_id, first_name, middle_name, last_name, email, phone,
                    birthdate, gender, address, admission_type, program_id, status,
                    student_id, submitted_at, updated_at, notes
                ) VALUES (
                    :id, :application_id, :first_name, :middle_name, :last_name, :email, :phone,
                    :birthdate, :gender, :address, :admission_type, :program_id, :status,
                    :student_id, :submitted_at, :updated_at, :notes
                )
            ");
            
            $restoreStmt->bindParam(':id', $archived['original_id']);
            $restoreStmt->bindParam(':application_id', $archived['application_id']);
            $restoreStmt->bindParam(':first_name', $archived['first_name']);
            $restoreStmt->bindParam(':middle_name', $archived['middle_name']);
            $restoreStmt->bindParam(':last_name', $archived['last_name']);
            $restoreStmt->bindParam(':email', $archived['email']);
            $restoreStmt->bindParam(':phone', $archived['phone']);
            $restoreStmt->bindParam(':birthdate', $archived['birthdate']);
            $restoreStmt->bindParam(':gender', $archived['gender']);
            $restoreStmt->bindParam(':address', $archived['address']);
            $restoreStmt->bindParam(':admission_type', $archived['admission_type']);
            $restoreStmt->bindParam(':program_id', $archived['program_id']);
            $restoreStmt->bindParam(':status', $archived['status']);
            $restoreStmt->bindParam(':student_id', $archived['student_id']);
            $restoreStmt->bindParam(':submitted_at', $archived['submitted_at']);
            $restoreStmt->bindParam(':updated_at', $archived['updated_at']);
            $restoreStmt->bindParam(':notes', $archived['notes']);
            
            $this->db->beginTransaction();
            
            if ($restoreStmt->execute()) {
                // Delete from archive
                $deleteArchiveStmt = $this->db->prepare("DELETE FROM archive_admissions WHERE id = :id");
                $deleteArchiveStmt->bindParam(':id', $archiveId);
                
                if ($deleteArchiveStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Admission restored successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to remove from archive'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to restore admission'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Archive a program head record
     */
    public function archiveProgramHead($programHeadId, $reason = 'Manual deletion') {
        try {
            // Get the program head record first
            $stmt = $this->db->prepare("SELECT * FROM program_heads WHERE id = :id");
            $stmt->bindParam(':id', $programHeadId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Program head not found'];
            }
            
            $programHead = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert into archive table
            $archiveStmt = $this->db->prepare("
                INSERT INTO archive_program_heads (
                    original_id, employee_id, first_name, middle_name, last_name, email, phone,
                    department, specialization, hire_date, status, created_at, updated_at, deleted_at, deleted_by, delete_reason
                ) VALUES (
                    :original_id, :employee_id, :first_name, :middle_name, :last_name, :email, :phone,
                    :department, :specialization, :hire_date, :status, :created_at, :updated_at, NOW(), :deleted_by, :delete_reason
                )
            ");
            
            $archiveStmt->bindParam(':original_id', $programHead['id']);
            $archiveStmt->bindParam(':employee_id', $programHead['employee_id']);
            $archiveStmt->bindParam(':first_name', $programHead['first_name']);
            $archiveStmt->bindParam(':middle_name', $programHead['middle_name']);
            $archiveStmt->bindParam(':last_name', $programHead['last_name']);
            $archiveStmt->bindParam(':email', $programHead['email']);
            $archiveStmt->bindParam(':phone', $programHead['phone']);
            $archiveStmt->bindParam(':department', $programHead['department']);
            $archiveStmt->bindParam(':specialization', $programHead['specialization']);
            $archiveStmt->bindParam(':hire_date', $programHead['hire_date']);
            $archiveStmt->bindParam(':status', $programHead['status']);
            $archiveStmt->bindParam(':created_at', $programHead['created_at']);
            $archiveStmt->bindParam(':updated_at', $programHead['updated_at']);
            $archiveStmt->bindParam(':deleted_by', $this->deletedBy);
            $archiveStmt->bindParam(':delete_reason', $reason);
            
            $this->db->beginTransaction();
            
            if ($archiveStmt->execute()) {
                // Delete the original record
                $deleteStmt = $this->db->prepare("DELETE FROM program_heads WHERE id = :id");
                $deleteStmt->bindParam(':id', $programHeadId);
                
                if ($deleteStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Program head archived successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to delete original program head'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to archive program head'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Archive a program record
     */
    public function archiveProgram($programId, $reason = 'Manual deletion') {
        try {
            // Get the program record first
            $stmt = $this->db->prepare("SELECT * FROM programs WHERE id = :id");
            $stmt->bindParam(':id', $programId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Program not found'];
            }
            
            $program = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert into archive table
            $archiveStmt = $this->db->prepare("
                INSERT INTO archive_programs (
                    original_id, program_code, program_title, description, department,
                    duration_years, tuition_fee, status, created_at, updated_at, deleted_at, deleted_by, delete_reason
                ) VALUES (
                    :original_id, :program_code, :program_title, :description, :department,
                    :duration_years, :tuition_fee, :status, :created_at, :updated_at, NOW(), :deleted_by, :delete_reason
                )
            ");
            
            $archiveStmt->bindParam(':original_id', $program['id']);
            $archiveStmt->bindParam(':program_code', $program['program_code']);
            $archiveStmt->bindParam(':program_title', $program['program_title']);
            $archiveStmt->bindParam(':description', $program['description']);
            $archiveStmt->bindParam(':department', $program['department']);
            $archiveStmt->bindParam(':duration_years', $program['duration_years']);
            $archiveStmt->bindParam(':tuition_fee', $program['tuition_fee']);
            $archiveStmt->bindParam(':status', $program['status']);
            $archiveStmt->bindParam(':created_at', $program['created_at']);
            $archiveStmt->bindParam(':updated_at', $program['updated_at']);
            $archiveStmt->bindParam(':deleted_by', $this->deletedBy);
            $archiveStmt->bindParam(':delete_reason', $reason);
            
            $this->db->beginTransaction();
            
            if ($archiveStmt->execute()) {
                // Delete the original record
                $deleteStmt = $this->db->prepare("DELETE FROM programs WHERE id = :id");
                $deleteStmt->bindParam(':id', $programId);
                
                if ($deleteStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Program archived successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to delete original program'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to archive program'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Archive a student record
     */
    public function archiveStudent($studentId, $reason = 'Manual deletion') {
        try {
            // Get the student record first
            $stmt = $this->db->prepare("SELECT * FROM students WHERE id = :id");
            $stmt->bindParam(':id', $studentId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Student not found'];
            }
            
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Insert into archive table
            $archiveStmt = $this->db->prepare("
                INSERT INTO archive_students (
                    original_id, student_id, first_name, middle_name, last_name, email, phone,
                    birth_date, gender, address, department, section_id, yearlevel,
                    status, avatar, created_at, updated_at, deleted_at, deleted_by, delete_reason
                ) VALUES (
                    :original_id, :student_id, :first_name, :middle_name, :last_name, :email, :phone,
                    :birth_date, :gender, :address, :department, :section_id, :yearlevel,
                    :status, :avatar, :created_at, :updated_at, NOW(), :deleted_by, :delete_reason
                )
            ");
            
            $archiveStmt->bindParam(':original_id', $student['id']);
            $archiveStmt->bindParam(':student_id', $student['student_id']);
            $archiveStmt->bindParam(':first_name', $student['first_name']);
            $archiveStmt->bindParam(':middle_name', $student['middle_name']);
            $archiveStmt->bindParam(':last_name', $student['last_name']);
            $archiveStmt->bindParam(':email', $student['email']);
            $archiveStmt->bindParam(':phone', $student['phone']);
            $archiveStmt->bindParam(':birth_date', $student['birth_date']);
            $archiveStmt->bindParam(':gender', $student['gender']);
            $archiveStmt->bindParam(':address', $student['address']);
            $archiveStmt->bindParam(':department', $student['department']);
            $archiveStmt->bindParam(':section_id', $student['section_id']);
            $archiveStmt->bindParam(':yearlevel', $student['yearlevel']);
            $archiveStmt->bindParam(':status', $student['status']);
            $archiveStmt->bindParam(':avatar', $student['avatar']);
            $archiveStmt->bindParam(':created_at', $student['created_at']);
            $archiveStmt->bindParam(':updated_at', $student['updated_at']);
            $archiveStmt->bindParam(':deleted_by', $this->deletedBy);
            $archiveStmt->bindParam(':delete_reason', $reason);
            
            $this->db->beginTransaction();
            
            if ($archiveStmt->execute()) {
                // Delete the original record
                $deleteStmt = $this->db->prepare("DELETE FROM students WHERE id = :id");
                $deleteStmt->bindParam(':id', $studentId);
                
                if ($deleteStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Student archived successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to delete original student'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to archive student'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get archived program heads
     */
    public function getArchivedProgramHeads($filters = []) {
        try {
            $sql = "SELECT * FROM archive_program_heads WHERE 1=1";
            $params = [];
            
            // Add filters
            if (!empty($filters['search'])) {
                $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR employee_id LIKE :search)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[':search'] = $searchTerm;
            }
            
            $sql .= " ORDER BY deleted_at DESC";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Restore archived program head
     */
    public function restoreProgramHead($archiveId) {
        try {
            // Get the archived record
            $stmt = $this->db->prepare("SELECT * FROM archive_program_heads WHERE id = :id");
            $stmt->bindParam(':id', $archiveId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Archived program head not found'];
            }
            
            $archived = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if original ID already exists
            $checkStmt = $this->db->prepare("SELECT id FROM program_heads WHERE id = :id");
            $checkStmt->bindParam(':id', $archived['original_id']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Original program head already exists'];
            }
            
            // Restore to original table
            $restoreStmt = $this->db->prepare("
                INSERT INTO program_heads (
                    id, employee_id, first_name, middle_name, last_name, email, phone,
                    department, specialization, hire_date, status, created_at, updated_at
                ) VALUES (
                    :id, :employee_id, :first_name, :middle_name, :last_name, :email, :phone,
                    :department, :specialization, :hire_date, :status, :created_at, :updated_at
                )
            ");
            
            $restoreStmt->bindParam(':id', $archived['original_id']);
            $restoreStmt->bindParam(':employee_id', $archived['employee_id']);
            $restoreStmt->bindParam(':first_name', $archived['first_name']);
            $restoreStmt->bindParam(':middle_name', $archived['middle_name']);
            $restoreStmt->bindParam(':last_name', $archived['last_name']);
            $restoreStmt->bindParam(':email', $archived['email']);
            $restoreStmt->bindParam(':phone', $archived['phone']);
            $restoreStmt->bindParam(':department', $archived['department']);
            $restoreStmt->bindParam(':specialization', $archived['specialization']);
            $restoreStmt->bindParam(':hire_date', $archived['hire_date']);
            $restoreStmt->bindParam(':status', $archived['status']);
            $restoreStmt->bindParam(':created_at', $archived['created_at']);
            $restoreStmt->bindParam(':updated_at', $archived['updated_at']);
            
            $this->db->beginTransaction();
            
            if ($restoreStmt->execute()) {
                // Delete from archive
                $deleteArchiveStmt = $this->db->prepare("DELETE FROM archive_program_heads WHERE id = :id");
                $deleteArchiveStmt->bindParam(':id', $archiveId);
                
                if ($deleteArchiveStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Program head restored successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to remove from archive'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to restore program head'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get all archived items with filtering
     */
    public function getAllArchivedItems($filters = []) {
        try {
            $results = [];
            
            // Get archived admissions
            $admissionsResult = $this->getArchivedAdmissions($filters);
            if ($admissionsResult['success']) {
                $results = array_merge($results, array_map(function($item) {
                    $item['item_type'] = 'admission';
                    $item['source_table'] = 'archive_admissions';
                    return $item;
                }, $admissionsResult['data']));
            }
            
            // Get archived programs
            $programsResult = $this->getArchivedPrograms($filters);
            if ($programsResult['success']) {
                $results = array_merge($results, array_map(function($item) {
                    $item['item_type'] = 'program';
                    $item['source_table'] = 'archive_programs';
                    return $item;
                }, $programsResult['data']));
            }
            
            // Get archived students
            $studentsResult = $this->getArchivedStudents($filters);
            if ($studentsResult['success']) {
                $results = array_merge($results, array_map(function($item) {
                    $item['item_type'] = 'student';
                    $item['source_table'] = 'archive_students';
                    return $item;
                }, $studentsResult['data']));
            }
            
            // Get archived program heads
            $programHeadsResult = $this->getArchivedProgramHeads($filters);
            if ($programHeadsResult['success']) {
                $results = array_merge($results, array_map(function($item) {
                    $item['item_type'] = 'program_head';
                    $item['source_table'] = 'archive_program_heads';
                    return $item;
                }, $programHeadsResult['data']));
            }
            
            // Sort by deleted_at
            usort($results, function($a, $b) {
                return strtotime($b['deleted_at']) - strtotime($a['deleted_at']);
            });
            
            return ['success' => true, 'data' => $results];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get archived programs
     */
    public function getArchivedPrograms($filters = []) {
        try {
            $sql = "SELECT * FROM archive_programs WHERE 1=1";
            $params = [];
            
            // Add filters
            if (!empty($filters['search'])) {
                $sql .= " AND (program_title LIKE :search OR program_code LIKE :search)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[':search'] = $searchTerm;
            }
            
            $sql .= " ORDER BY deleted_at DESC";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get archived students
     */
    public function getArchivedStudents($filters = []) {
        try {
            $sql = "SELECT * FROM archive_students WHERE 1=1";
            $params = [];
            
            // Add filters
            if (!empty($filters['status'])) {
                $sql .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR student_id LIKE :search)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[':search'] = $searchTerm;
            }
            
            $sql .= " ORDER BY deleted_at DESC";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Restore archived program
     */
    public function restoreProgram($archiveId) {
        try {
            // Get the archived record
            $stmt = $this->db->prepare("SELECT * FROM archive_programs WHERE id = :id");
            $stmt->bindParam(':id', $archiveId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Archived program not found'];
            }
            
            $archived = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if original ID already exists
            $checkStmt = $this->db->prepare("SELECT id FROM programs WHERE id = :id");
            $checkStmt->bindParam(':id', $archived['original_id']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Original program already exists'];
            }
            
            // Restore to original table
            $restoreStmt = $this->db->prepare("
                INSERT INTO programs (
                    id, program_code, program_title, description, department, duration_years,
                    tuition_fee, status, created_at, updated_at
                ) VALUES (
                    :id, :program_code, :program_title, :description, :department, :duration_years,
                    :tuition_fee, :status, :created_at, :updated_at
                )
            ");
            
            $restoreStmt->bindParam(':id', $archived['original_id']);
            $restoreStmt->bindParam(':program_code', $archived['program_code']);
            $restoreStmt->bindParam(':program_title', $archived['program_title']);
            $restoreStmt->bindParam(':description', $archived['description']);
            $restoreStmt->bindParam(':department', $archived['department']);
            $restoreStmt->bindParam(':duration_years', $archived['duration_years']);
            $restoreStmt->bindParam(':tuition_fee', $archived['tuition_fee']);
            $restoreStmt->bindParam(':status', $archived['status']);
            $restoreStmt->bindParam(':created_at', $archived['created_at']);
            $restoreStmt->bindParam(':updated_at', $archived['updated_at']);
            
            $this->db->beginTransaction();
            
            if ($restoreStmt->execute()) {
                // Delete from archive
                $deleteArchiveStmt = $this->db->prepare("DELETE FROM archive_programs WHERE id = :id");
                $deleteArchiveStmt->bindParam(':id', $archiveId);
                
                if ($deleteArchiveStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Program restored successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to remove from archive'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to restore program'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Restore archived student
     */
    public function restoreStudent($archiveId) {
        try {
            // Get the archived record
            $stmt = $this->db->prepare("SELECT * FROM archive_students WHERE id = :id");
            $stmt->bindParam(':id', $archiveId);
            $stmt->execute();
            
            if ($stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Archived student not found'];
            }
            
            $archived = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Check if original ID already exists
            $checkStmt = $this->db->prepare("SELECT id FROM students WHERE id = :id");
            $checkStmt->bindParam(':id', $archived['original_id']);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Original student already exists'];
            }
            
            // Restore to original table
            $restoreStmt = $this->db->prepare("
                INSERT INTO students (
                    id, student_id, first_name, middle_name, last_name, email, phone,
                    birth_date, gender, address, department, section_id, yearlevel,
                    status, avatar, created_at, updated_at
                ) VALUES (
                    :id, :student_id, :first_name, :middle_name, :last_name, :email, :phone,
                    :birth_date, :gender, :address, :department, :section_id, :yearlevel,
                    :status, :avatar, :created_at, :updated_at
                )
            ");
            
            $restoreStmt->bindParam(':id', $archived['original_id']);
            $restoreStmt->bindParam(':student_id', $archived['student_id']);
            $restoreStmt->bindParam(':first_name', $archived['first_name']);
            $restoreStmt->bindParam(':middle_name', $archived['middle_name']);
            $restoreStmt->bindParam(':last_name', $archived['last_name']);
            $restoreStmt->bindParam(':email', $archived['email']);
            $restoreStmt->bindParam(':phone', $archived['phone']);
            $restoreStmt->bindParam(':birth_date', $archived['birth_date']);
            $restoreStmt->bindParam(':gender', $archived['gender']);
            $restoreStmt->bindParam(':address', $archived['address']);
            $restoreStmt->bindParam(':department', $archived['department']);
            $restoreStmt->bindParam(':section_id', $archived['section_id']);
            $restoreStmt->bindParam(':yearlevel', $archived['year_level']);
            $restoreStmt->bindParam(':status', $archived['status']);
            $restoreStmt->bindParam(':avatar', null); // Not in archive table
            $restoreStmt->bindParam(':created_at', $archived['created_at']);
            $restoreStmt->bindParam(':updated_at', $archived['updated_at']);
            
            $this->db->beginTransaction();
            
            if ($restoreStmt->execute()) {
                // Delete from archive
                $deleteArchiveStmt = $this->db->prepare("DELETE FROM archive_students WHERE id = :id");
                $deleteArchiveStmt->bindParam(':id', $archiveId);
                
                if ($deleteArchiveStmt->execute()) {
                    $this->db->commit();
                    return ['success' => true, 'message' => 'Student restored successfully'];
                } else {
                    $this->db->rollBack();
                    return ['success' => false, 'message' => 'Failed to remove from archive'];
                }
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to restore student'];
            }
            
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get archived admissions with filtering
     */
    public function getArchivedAdmissions($filters = []) {
        try {
            $sql = "SELECT * FROM archive_admissions WHERE 1=1";
            $params = [];
            
            // Add filters
            if (!empty($filters['item_type'])) {
                $sql .= " AND admission_type = :admission_type";
                $params[':admission_type'] = $filters['item_type'];
            }
            
            if (!empty($filters['status'])) {
                $sql .= " AND status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $sql .= " AND deleted_at >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $sql .= " AND deleted_at <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            if (!empty($filters['search'])) {
                $sql .= " AND (first_name LIKE :search OR last_name LIKE :search OR email LIKE :search OR application_id LIKE :search)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[':search'] = $searchTerm;
            }
            
            $sql .= " ORDER BY deleted_at DESC";
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();
            
            return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Permanently delete archived record
     */
    public function permanentDelete($archiveId, $table) {
        try {
            $validTables = ['archive_admissions', 'archive_programs', 'archive_program_heads', 'archive_students'];
            
            if (!in_array($table, $validTables)) {
                return ['success' => false, 'message' => 'Invalid table'];
            }
            
            $stmt = $this->db->prepare("DELETE FROM $table WHERE id = :id");
            $stmt->bindParam(':id', $archiveId);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Record permanently deleted'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete record'];
            }
            
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
?>
