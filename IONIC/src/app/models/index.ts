// Model untuk Data User
export interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'student' ;
  avatar?: string;
  created_at?: string;
}

export interface Category {
  id: number;
  name: string;
  icon?: string;
}

export interface Course {
  id: number;
  title: string;
  slug: string;
  description: string;
  price: number;
  thumbnail: string;
  instructor_name: string;
  category_id: number;
  category?: Category;
  rating: number;
  total_students: number;
  modules?: CourseModule[];
}

export interface CourseModule {
  id: number;
  course_id: number;
  title: string;
  video_url?: string;
  duration?: string;
  is_locked: boolean; 
}