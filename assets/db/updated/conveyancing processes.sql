-- Conveyancing Process Stages Table
CREATE TABLE conveyancing_process_stages (
    stage_id INT IDENTITY(1,1) PRIMARY KEY,
    process_id INT NOT NULL,
    title NVARCHAR(100) NOT NULL,
    description NVARCHAR(500),
    sequence_order INT NOT NULL,
    estimated_duration INT,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);

-- Conveyancing Process Instances Table
CREATE TABLE conveyancing_process_instances (
    instance_id INT IDENTITY(1,1) PRIMARY KEY,
    process_id INT NOT NULL,
    reference_number NVARCHAR(50) UNIQUE,
    current_stage_id INT,
    status NVARCHAR(20) DEFAULT 'active',
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (current_stage_id) REFERENCES conveyancing_process_stages(stage_id)
);

-- Conveyancing Stage Progress Table
CREATE TABLE conveyancing_stage_progress (
    progress_id INT IDENTITY(1,1) PRIMARY KEY,
    instance_id INT NOT NULL,
    stage_id INT NOT NULL,
    status NVARCHAR(20) NOT NULL,
    start_date DATETIME,
    completion_date DATETIME,
    details NVARCHAR(MAX),
    updated_by INT NOT NULL,
    updated_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (instance_id) REFERENCES conveyancing_process_instances(instance_id),
    FOREIGN KEY (stage_id) REFERENCES conveyancing_process_stages(stage_id)
);

-- Conveyancing Stage Updates Table
CREATE TABLE conveyancing_stage_updates (
    update_id INT IDENTITY(1,1) PRIMARY KEY,
    progress_id INT NOT NULL,
    update_type NVARCHAR(50) NOT NULL,
    old_status NVARCHAR(20),
    new_status NVARCHAR(20),
    details NVARCHAR(MAX),
    document_path NVARCHAR(255),
    created_by INT NOT NULL,
    created_at DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (progress_id) REFERENCES conveyancing_stage_progress(progress_id)
);


-- Indexes for conveyancing_process_stages
CREATE INDEX idx_conveyancing_process_stages_process ON conveyancing_process_stages(process_id);
CREATE INDEX idx_conveyancing_process_stages_sequence ON conveyancing_process_stages(process_id, sequence_order);

-- Indexes for conveyancing_process_instances
CREATE INDEX idx_conveyancing_process_instances_process ON conveyancing_process_instances(process_id);
CREATE INDEX idx_conveyancing_process_instances_status ON conveyancing_process_instances(status);
CREATE INDEX idx_conveyancing_process_instances_current_stage ON conveyancing_process_instances(current_stage_id);
CREATE INDEX idx_conveyancing_process_instances_created ON conveyancing_process_instances(created_at);

-- Indexes for conveyancing_stage_progress
CREATE INDEX idx_conveyancing_stage_progress_instance ON conveyancing_stage_progress(instance_id);
CREATE INDEX idx_conveyancing_stage_progress_stage ON conveyancing_stage_progress(stage_id);
CREATE INDEX idx_conveyancing_stage_progress_status ON conveyancing_stage_progress(status);
CREATE INDEX idx_conveyancing_stage_progress_instance_stage ON conveyancing_stage_progress(instance_id, stage_id);
CREATE INDEX idx_conveyancing_stage_progress_dates ON conveyancing_stage_progress(start_date, completion_date);
CREATE INDEX idx_conveyancing_stage_progress_instance_status ON conveyancing_stage_progress(instance_id, status);

-- Indexes for conveyancing_stage_updates
CREATE INDEX idx_conveyancing_stage_updates_progress ON conveyancing_stage_updates(progress_id);
CREATE INDEX idx_conveyancing_stage_updates_type ON conveyancing_stage_updates(update_type);
CREATE INDEX idx_conveyancing_stage_updates_created ON conveyancing_stage_updates(created_at DESC);
CREATE INDEX idx_conveyancing_stage_updates_progress_type ON conveyancing_stage_updates(progress_id, update_type);
CREATE INDEX idx_conveyancing_stage_updates_status_changes ON conveyancing_stage_updates(progress_id, old_status, new_status);