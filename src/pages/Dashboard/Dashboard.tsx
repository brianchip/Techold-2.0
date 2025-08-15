import React, { useState } from 'react';
import {
  Box,
  Grid,
  Card,
  CardContent,
  Typography,
  Chip,
  IconButton,
  Menu,
  MenuItem,
  List,
  ListItem,
  ListItemText,
  ListItemIcon,
  Divider,
  Button,
  useTheme,
  useMediaQuery,
} from '@mui/material';
import {
  Dashboard as DashboardIcon,
  Assignment as ProjectIcon,
  Schedule as TaskIcon,
  People as ResourceIcon,
  AttachMoney as BudgetIcon,
  Warning as RiskIcon,
  TrendingUp as TrendingUpIcon,
  TrendingDown as TrendingDownIcon,
  MoreVert as MoreVertIcon,
  Add as AddIcon,
  CheckCircle as CheckCircleIcon,
  Error as ErrorIcon,
  Info as InfoIcon,
} from '@mui/icons-material';
import { useQuery } from 'react-query';
import { useNavigate } from 'react-router-dom';
import { LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, PieChart, Pie, Cell } from 'recharts';
import { format } from 'date-fns';

// Services
import { projectService } from '../../services/projectService';

// Types
import { Project, ProjectStatistics } from '../../types/project';

// Components
import ProjectCard from '../../components/ProjectCard/ProjectCard';
import LoadingSpinner from '../../components/LoadingSpinner/LoadingSpinner';

const Dashboard: React.FC = () => {
  const theme = useTheme();
  const isMobile = useMediaQuery(theme.breakpoints.down('md'));
  const navigate = useNavigate();
  const [anchorEl, setAnchorEl] = useState<null | HTMLElement>(null);

  // Fetch dashboard data
  const { data: statistics, isLoading: statsLoading } = useQuery<ProjectStatistics>(
    'dashboard-statistics',
    projectService.getStatistics
  );

  const { data: recentProjects, isLoading: projectsLoading } = useQuery<Project[]>(
    'recent-projects',
    () => projectService.getRecentProjects(5)
  );

  const { data: overdueProjects, isLoading: overdueLoading } = useQuery<Project[]>(
    'overdue-projects',
    projectService.getOverdueProjects
  );

  // Mock data for charts (replace with real data)
  const projectProgressData = [
    { name: 'Jan', completed: 12, inProgress: 8, planned: 5 },
    { name: 'Feb', completed: 15, inProgress: 10, planned: 3 },
    { name: 'Mar', completed: 18, inProgress: 12, planned: 7 },
    { name: 'Apr', completed: 22, inProgress: 15, planned: 4 },
    { name: 'May', completed: 25, inProgress: 18, planned: 6 },
    { name: 'Jun', completed: 28, inProgress: 20, planned: 8 },
  ];

  const projectTypeData = [
    { name: 'Engineering', value: 35, color: '#8884d8' },
    { name: 'Procurement', value: 25, color: '#82ca9d' },
    { name: 'Installation', value: 20, color: '#ffc658' },
    { name: 'EPC', value: 20, color: '#ff7300' },
  ];

  const handleMenuOpen = (event: React.MouseEvent<HTMLElement>) => {
    setAnchorEl(event.currentTarget);
  };

  const handleMenuClose = () => {
    setAnchorEl(null);
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Completed':
        return 'success';
      case 'In Progress':
        return 'primary';
      case 'Planned':
        return 'info';
      case 'On Hold':
        return 'warning';
      case 'Overdue':
        return 'error';
      default:
        return 'default';
    }
  };

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'Completed':
        return <CheckCircleIcon fontSize="small" />;
      case 'In Progress':
        return <TrendingUpIcon fontSize="small" />;
      case 'Planned':
        return <InfoIcon fontSize="small" />;
      case 'On Hold':
        return <ErrorIcon fontSize="small" />;
      case 'Overdue':
        return <ErrorIcon fontSize="small" />;
      default:
        return <InfoIcon fontSize="small" />;
    }
  };

  if (statsLoading || projectsLoading || overdueLoading) {
    return <LoadingSpinner />;
  }

  return (
    <Box sx={{ p: 3 }}>
      {/* Header */}
      <Box sx={{ mb: 3, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <Box>
          <Typography variant="h4" component="h1" gutterBottom>
            Dashboard
          </Typography>
          <Typography variant="body1" color="text.secondary">
            Welcome back! Here's an overview of your projects and key metrics.
          </Typography>
        </Box>
        <Button
          variant="contained"
          startIcon={<AddIcon />}
          onClick={() => navigate('/projects/new')}
        >
          New Project
        </Button>
      </Box>

      {/* KPI Cards */}
      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} sm={6} md={3}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Box>
                  <Typography color="text.secondary" gutterBottom variant="body2">
                    Total Projects
                  </Typography>
                  <Typography variant="h4" component="div">
                    {statistics?.total_projects || 0}
                  </Typography>
                </Box>
                <ProjectIcon color="primary" sx={{ fontSize: 40 }} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Box>
                  <Typography color="text.secondary" gutterBottom variant="body2">
                    Active Projects
                  </Typography>
                  <Typography variant="h4" component="div">
                    {statistics?.active_projects || 0}
                  </Typography>
                </Box>
                <TaskIcon color="primary" sx={{ fontSize: 40 }} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Box>
                  <Typography color="text.secondary" gutterBottom variant="body2">
                    Total Budget
                  </Typography>
                  <Typography variant="h4" component="div">
                    ${(statistics?.total_budget || 0).toLocaleString()}
                  </Typography>
                </Box>
                <BudgetIcon color="primary" sx={{ fontSize: 40 }} />
              </Box>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} sm={6} md={3}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <Box>
                  <Typography color="text.secondary" gutterBottom variant="body2">
                    Overdue Projects
                  </Typography>
                  <Typography variant="h4" component="div" color="error.main">
                    {statistics?.overdue_projects || 0}
                  </Typography>
                </Box>
                <RiskIcon color="error" sx={{ fontSize: 40 }} />
              </Box>
            </CardContent>
          </Card>
        </Grid>
      </Grid>

      {/* Charts Section */}
      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} lg={8}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Project Progress Trend
              </Typography>
              <ResponsiveContainer width="100%" height={300}>
                <LineChart data={projectProgressData}>
                  <CartesianGrid strokeDasharray="3 3" />
                  <XAxis dataKey="name" />
                  <YAxis />
                  <Tooltip />
                  <Line type="monotone" dataKey="completed" stroke="#4caf50" strokeWidth={2} />
                  <Line type="monotone" dataKey="inProgress" stroke="#2196f3" strokeWidth={2} />
                  <Line type="monotone" dataKey="planned" stroke="#ff9800" strokeWidth={2} />
                </LineChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} lg={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Projects by Type
              </Typography>
              <ResponsiveContainer width="100%" height={300}>
                <PieChart>
                  <Pie
                    data={projectTypeData}
                    cx="50%"
                    cy="50%"
                    labelLine={false}
                    label={({ name, percent }) => `${name} ${(percent * 100).toFixed(0)}%`}
                    outerRadius={80}
                    fill="#8884d8"
                    dataKey="value"
                  >
                    {projectTypeData.map((entry, index) => (
                      <Cell key={`cell-${index}`} fill={entry.color} />
                    ))}
                  </Pie>
                  <Tooltip />
                </PieChart>
              </ResponsiveContainer>
            </CardContent>
          </Card>
        </Grid>
      </Grid>

      {/* Recent Projects and Overdue Projects */}
      <Grid container spacing={3}>
        <Grid item xs={12} lg={8}>
          <Card>
            <CardContent>
              <Box sx={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', mb: 2 }}>
                <Typography variant="h6">Recent Projects</Typography>
                <Button size="small" onClick={() => navigate('/projects')}>
                  View All
                </Button>
              </Box>
              <List>
                {recentProjects?.map((project, index) => (
                  <React.Fragment key={project.id}>
                    <ListItem>
                      <ListItemIcon>
                        {getStatusIcon(project.status)}
                      </ListItemIcon>
                      <ListItemText
                        primary={project.project_name}
                        secondary={`${project.project_code} • ${project.project_type}`}
                      />
                      <Chip
                        label={project.status}
                        color={getStatusColor(project.status) as any}
                        size="small"
                      />
                    </ListItem>
                    {index < recentProjects.length - 1 && <Divider />}
                  </React.Fragment>
                ))}
              </List>
            </CardContent>
          </Card>
        </Grid>

        <Grid item xs={12} lg={4}>
          <Card>
            <CardContent>
              <Typography variant="h6" gutterBottom>
                Overdue Projects
              </Typography>
              {overdueProjects && overdueProjects.length > 0 ? (
                <List>
                  {overdueProjects.map((project, index) => (
                    <React.Fragment key={project.id}>
                      <ListItem>
                        <ListItemText
                          primary={project.project_name}
                          secondary={`Due: ${format(new Date(project.end_date), 'MMM dd, yyyy')}`}
                        />
                        <Chip
                          label={`${project.days_remaining} days overdue`}
                          color="error"
                          size="small"
                        />
                      </ListItem>
                      {index < overdueProjects.length - 1 && <Divider />}
                    </React.Fragment>
                  ))}
                </List>
              ) : (
                <Box sx={{ textAlign: 'center', py: 2 }}>
                  <Typography variant="body2" color="text.secondary">
                    No overdue projects
                  </Typography>
                </Box>
              )}
            </CardContent>
          </Card>
        </Grid>
      </Grid>
    </Box>
  );
};

export default Dashboard;
