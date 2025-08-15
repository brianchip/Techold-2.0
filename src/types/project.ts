// Base entity interface
export interface BaseEntity {
  id: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
}

// Project types
export interface Project extends BaseEntity {
  project_code: string;
  project_name: string;
  project_type: ProjectType;
  client_id: number;
  start_date: string;
  end_date: string;
  status: ProjectStatus;
  project_manager_id: number;
  description?: string;
  total_budget: number;
  actual_cost: number;
  progress_percent: number;
  location?: string;
  metadata?: Record<string, any>;
  
  // Computed properties
  duration?: number;
  variance?: number;
  variance_percent?: number;
  is_overdue?: boolean;
  days_remaining?: number;
  
  // Relationships
  client?: Client;
  project_manager?: Employee;
  tasks?: Task[];
  resources?: Resource[];
  budget_lines?: BudgetLine[];
  documents?: Document[];
  risks?: Risk[];
}

export type ProjectType = 'Engineering' | 'Procurement' | 'Installation' | 'EPC';
export type ProjectStatus = 'Planned' | 'In Progress' | 'Completed' | 'On Hold' | 'Cancelled';

// Task types
export interface Task extends BaseEntity {
  project_id: number;
  parent_task_id?: number;
  task_name: string;
  description?: string;
  start_date: string;
  end_date: string;
  planned_cost: number;
  actual_cost: number;
  progress_percent: number;
  dependency_type?: DependencyType;
  dependency_task_id?: number;
  status: TaskStatus;
  priority: TaskPriority;
  estimated_hours?: number;
  actual_hours?: number;
  metadata?: Record<string, any>;
  
  // Relationships
  project?: Project;
  parent_task?: Task;
  dependency_task?: Task;
  sub_tasks?: Task[];
  resources?: Resource[];
  documents?: Document[];
}

export type DependencyType = 'FS' | 'SS' | 'FF' | 'SF'; // Finish-Start, Start-Start, Finish-Finish, Start-Finish
export type TaskStatus = 'Not Started' | 'In Progress' | 'Completed' | 'On Hold' | 'Cancelled';
export type TaskPriority = 1 | 2 | 3; // 1=High, 2=Medium, 3=Low

// Resource types
export interface Resource extends BaseEntity {
  task_id: number;
  employee_id?: number;
  equipment_id?: number;
  role?: string;
  allocated_hours: number;
  actual_hours: number;
  hourly_rate?: number;
  total_cost: number;
  allocation_start_date: string;
  allocation_end_date: string;
  status: ResourceStatus;
  notes?: string;
  
  // Relationships
  task?: Task;
  employee?: Employee;
  equipment?: Equipment;
}

export type ResourceStatus = 'Allocated' | 'Active' | 'Completed' | 'Cancelled';

// Budget Line types
export interface BudgetLine extends BaseEntity {
  project_id: number;
  task_id?: number;
  category: BudgetCategory;
  description: string;
  unit?: string;
  quantity: number;
  unit_cost: number;
  planned_amount: number;
  actual_amount: number;
  variance: number;
  variance_percent: number;
  status: BudgetStatus;
  boq_reference?: string;
  supplier_reference?: string;
  planned_date?: string;
  actual_date?: string;
  notes?: string;
  
  // Relationships
  project?: Project;
  task?: Task;
}

export type BudgetCategory = 'Material' | 'Labor' | 'Overhead' | 'Equipment' | 'Subcontractor' | 'Other';
export type BudgetStatus = 'Planned' | 'Approved' | 'In Progress' | 'Completed' | 'Cancelled';

// Document types
export interface Document extends BaseEntity {
  project_id: number;
  task_id?: number;
  file_name: string;
  original_file_name: string;
  file_path: string;
  file_url?: string;
  file_type: string;
  file_size: number;
  category: DocumentCategory;
  version: string;
  description?: string;
  uploaded_by: number;
  uploaded_at: string;
  metadata?: Record<string, any>;
  is_public: boolean;
  
  // Relationships
  project?: Project;
  task?: Task;
  uploader?: Employee;
}

export type DocumentCategory = 
  | 'Contracts & BOQs'
  | 'Design & Drawings'
  | 'Site Surveys'
  | 'Procurement & Invoices'
  | 'Progress Reports'
  | 'SHEQ'
  | 'Photos & Media'
  | 'Meeting Minutes'
  | 'Other';

// Risk types
export interface Risk extends BaseEntity {
  project_id: number;
  task_id?: number;
  risk_title: string;
  description: string;
  severity: RiskSeverity;
  probability: RiskProbability;
  impact: RiskImpact;
  risk_score: number;
  mitigation_plan?: string;
  contingency_plan?: string;
  status: RiskStatus;
  assigned_to?: number;
  target_mitigation_date?: string;
  actual_mitigation_date?: string;
  mitigation_cost?: number;
  notes?: string;
  
  // Relationships
  project?: Project;
  task?: Task;
  assignee?: Employee;
}

export type RiskSeverity = 'Low' | 'Medium' | 'High' | 'Critical';
export type RiskProbability = 'Very Low' | 'Low' | 'Medium' | 'High' | 'Very High';
export type RiskImpact = 'Low' | 'Medium' | 'High' | 'Critical';
export type RiskStatus = 'Identified' | 'Assessed' | 'Mitigated' | 'Monitored' | 'Closed';

// Integration types (for other ERP modules)
export interface Client extends BaseEntity {
  name: string;
  email: string;
  phone?: string;
  address?: string;
  company_name?: string;
  contact_person?: string;
}

export interface Employee extends BaseEntity {
  first_name: string;
  last_name: string;
  email: string;
  employee_code: string;
  position?: string;
  department?: string;
  hourly_rate?: number;
  is_active: boolean;
}

export interface Equipment extends BaseEntity {
  name: string;
  equipment_code: string;
  type: string;
  model?: string;
  serial_number?: string;
  hourly_rate?: number;
  is_available: boolean;
}

// Request/Response types
export interface CreateProjectRequest {
  project_name: string;
  project_type: ProjectType;
  client_id: number;
  start_date: string;
  end_date: string;
  project_manager_id: number;
  description?: string;
  total_budget?: number;
  location?: string;
  metadata?: Record<string, any>;
  tasks?: Omit<CreateTaskRequest, 'project_id'>[];
  budget_lines?: Omit<CreateBudgetLineRequest, 'project_id'>[];
}

export interface UpdateProjectRequest extends Partial<CreateProjectRequest> {
  status?: ProjectStatus;
  progress_percent?: number;
}

export interface CreateTaskRequest {
  project_id: number;
  parent_task_id?: number;
  task_name: string;
  description?: string;
  start_date: string;
  end_date: string;
  planned_cost?: number;
  priority?: TaskPriority;
  estimated_hours?: number;
  dependency_type?: DependencyType;
  dependency_task_id?: number;
}

export interface UpdateTaskRequest extends Partial<CreateTaskRequest> {
  status?: TaskStatus;
  progress_percent?: number;
  actual_cost?: number;
  actual_hours?: number;
}

export interface CreateResourceRequest {
  task_id: number;
  employee_id?: number;
  equipment_id?: number;
  role?: string;
  allocated_hours: number;
  hourly_rate?: number;
  allocation_start_date: string;
  allocation_end_date: string;
  notes?: string;
}

export interface CreateBudgetLineRequest {
  project_id: number;
  task_id?: number;
  category: BudgetCategory;
  description: string;
  unit?: string;
  quantity: number;
  unit_cost: number;
  planned_amount: number;
  boq_reference?: string;
  planned_date?: string;
  notes?: string;
}

export interface CreateDocumentRequest {
  project_id: number;
  task_id?: number;
  file: File;
  category: DocumentCategory;
  description?: string;
  is_public?: boolean;
}

export interface CreateRiskRequest {
  project_id: number;
  task_id?: number;
  risk_title: string;
  description: string;
  severity: RiskSeverity;
  probability: RiskProbability;
  impact: RiskImpact;
  mitigation_plan?: string;
  contingency_plan?: string;
  assigned_to?: number;
  target_mitigation_date?: string;
  mitigation_cost?: number;
  notes?: string;
}

// Statistics and Dashboard types
export interface ProjectStatistics {
  total_projects: number;
  active_projects: number;
  completed_projects: number;
  overdue_projects: number;
  total_budget: number;
  total_actual_cost: number;
  projects_by_type: Array<{ project_type: ProjectType; count: number }>;
  projects_by_status: Array<{ status: ProjectStatus; count: number }>;
}

export interface DashboardData {
  statistics: ProjectStatistics;
  recent_projects: Project[];
  overdue_projects: Project[];
  upcoming_deadlines: Task[];
  critical_risks: Risk[];
  budget_variance: {
    total_planned: number;
    total_actual: number;
    variance: number;
    variance_percent: number;
  };
}

// Filter and Search types
export interface ProjectFilters {
  status?: ProjectStatus;
  type?: ProjectType;
  manager_id?: number;
  client_id?: number;
  search?: string;
  overdue?: boolean;
  date_range?: {
    start_date?: string;
    end_date?: string;
  };
}

export interface TaskFilters {
  project_id?: number;
  status?: TaskStatus;
  priority?: TaskPriority;
  search?: string;
  date_range?: {
    start_date?: string;
    end_date?: string;
  };
}

export interface ResourceFilters {
  task_id?: number;
  employee_id?: number;
  equipment_id?: number;
  status?: ResourceStatus;
  date_range?: {
    start_date?: string;
    end_date?: string;
  };
}

// Pagination types
export interface PaginationParams {
  page?: number;
  per_page?: number;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number;
  to: number;
}

// API Response types
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  error?: string;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
  status: number;
}
