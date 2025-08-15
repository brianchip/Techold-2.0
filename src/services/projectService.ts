import axios from 'axios';
import { Project, ProjectStatistics, CreateProjectRequest, UpdateProjectRequest } from '../types/project';

// API base URL
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost:8000/api';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Request interceptor to add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Unauthorized - redirect to login
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export const projectService = {
  /**
   * Get all projects with filtering and pagination
   */
  async getProjects(params: {
    page?: number;
    per_page?: number;
    status?: string;
    type?: string;
    manager_id?: number;
    client_id?: number;
    search?: string;
    sort_by?: string;
    sort_direction?: 'asc' | 'desc';
    overdue?: boolean;
  } = {}): Promise<{
    data: Project[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  }> {
    try {
      const response = await apiClient.get('/projects', { params });
      return response.data.data;
    } catch (error) {
      console.error('Error fetching projects:', error);
      throw error;
    }
  },

  /**
   * Get a single project by ID
   */
  async getProject(id: number): Promise<Project> {
    try {
      const response = await apiClient.get(`/projects/${id}`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project:', error);
      throw error;
    }
  },

  /**
   * Create a new project
   */
  async createProject(projectData: CreateProjectRequest): Promise<Project> {
    try {
      const response = await apiClient.post('/projects', projectData);
      return response.data.data;
    } catch (error) {
      console.error('Error creating project:', error);
      throw error;
    }
  },

  /**
   * Update an existing project
   */
  async updateProject(id: number, projectData: UpdateProjectRequest): Promise<Project> {
    try {
      const response = await apiClient.put(`/projects/${id}`, projectData);
      return response.data.data;
    } catch (error) {
      console.error('Error updating project:', error);
      throw error;
    }
  },

  /**
   * Delete a project
   */
  async deleteProject(id: number): Promise<void> {
    try {
      await apiClient.delete(`/projects/${id}`);
    } catch (error) {
      console.error('Error deleting project:', error);
      throw error;
    }
  },

  /**
   * Get project statistics
   */
  async getStatistics(): Promise<ProjectStatistics> {
    try {
      const response = await apiClient.get('/projects/statistics');
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project statistics:', error);
      throw error;
    }
  },

  /**
   * Get recent projects
   */
  async getRecentProjects(limit: number = 5): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: {
          per_page: limit,
          sort_by: 'created_at',
          sort_direction: 'desc',
        },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error fetching recent projects:', error);
      throw error;
    }
  },

  /**
   * Get overdue projects
   */
  async getOverdueProjects(): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: {
          overdue: true,
          status: 'In Progress',
        },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error fetching overdue projects:', error);
      throw error;
    }
  },

  /**
   * Get projects by status
   */
  async getProjectsByStatus(status: string): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: { status },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error fetching projects by status:', error);
      throw error;
    }
  },

  /**
   * Get projects by type
   */
  async getProjectsByType(type: string): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: { type },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error fetching projects by type:', error);
      throw error;
    }
  },

  /**
   * Get projects by manager
   */
  async getProjectsByManager(managerId: number): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: { manager_id: managerId },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error fetching projects by manager:', error);
      throw error;
    }
  },

  /**
   * Search projects
   */
  async searchProjects(query: string): Promise<Project[]> {
    try {
      const response = await apiClient.get('/projects', {
        params: { search: query },
      });
      return response.data.data.data;
    } catch (error) {
      console.error('Error searching projects:', error);
      throw error;
    }
  },

  /**
   * Export projects
   */
  async exportProjects(format: 'excel' | 'pdf' = 'excel'): Promise<Blob> {
    try {
      const response = await apiClient.get('/projects/export', {
        params: { format },
        responseType: 'blob',
      });
      return response.data;
    } catch (error) {
      console.error('Error exporting projects:', error);
      throw error;
    }
  },

  /**
   * Get project tasks
   */
  async getProjectTasks(projectId: number): Promise<any[]> {
    try {
      const response = await apiClient.get(`/projects/${projectId}/tasks`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project tasks:', error);
      throw error;
    }
  },

  /**
   * Get project resources
   */
  async getProjectResources(projectId: number): Promise<any[]> {
    try {
      const response = await apiClient.get(`/projects/${projectId}/resources`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project resources:', error);
      throw error;
    }
  },

  /**
   * Get project budget lines
   */
  async getProjectBudgetLines(projectId: number): Promise<any[]> {
    try {
      const response = await apiClient.get(`/projects/${projectId}/budget-lines`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project budget lines:', error);
      throw error;
    }
  },

  /**
   * Get project documents
   */
  async getProjectDocuments(projectId: number): Promise<any[]> {
    try {
      const response = await apiClient.get(`/projects/${projectId}/documents`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project documents:', error);
      throw error;
    }
  },

  /**
   * Get project risks
   */
  async getProjectRisks(projectId: number): Promise<any[]> {
    try {
      const response = await apiClient.get(`/projects/${projectId}/risks`);
      return response.data.data;
    } catch (error) {
      console.error('Error fetching project risks:', error);
      throw error;
    }
  },

  /**
   * Update project progress
   */
  async updateProjectProgress(projectId: number): Promise<void> {
    try {
      await apiClient.post(`/projects/${projectId}/update-progress`);
    } catch (error) {
      console.error('Error updating project progress:', error);
      throw error;
    }
  },

  /**
   * Calculate project actual cost
   */
  async calculateProjectActualCost(projectId: number): Promise<void> {
    try {
      await apiClient.post(`/projects/${projectId}/calculate-cost`);
    } catch (error) {
      console.error('Error calculating project actual cost:', error);
      throw error;
    }
  },
};

export default projectService;
