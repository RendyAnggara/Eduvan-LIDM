import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { Router } from '@angular/router';
import { AuthService } from '../services/auth';
import { SearchService } from '../services/search';
import { CourseService } from '../services/course.service';

@Component({
  selector: 'app-beranda',
  templateUrl: './home.page.html',
  styleUrls: ['./home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  namaUser: string = 'User';
  keywordPencarian: string = '';
  isLoading: boolean = true;
  kursusTersaring: any[] = [];
  unreadCount: number = 0;
  selectedCategory: string | null = null;
  courses: any[] = [];
  coursesWajib: any[] = [];
  coursesPilihan: any[] = [];

  constructor(
    private router: Router,
    private authService: AuthService,
    private searchService: SearchService,
    private courseService: CourseService,
    private cdr: ChangeDetectorRef
  ) {}

  ngOnInit() {
    this.ambilNamaUserLive();
    this.muatDataBerandaTotal();

    this.courseService.notifChanged$.subscribe((berubah: boolean) => {
      if (berubah) {
        this.muatJumlahNotifikasi();
      }
    });
  }

  selectCategory(kategori: string) {
    if (this.selectedCategory === kategori) {
      this.selectedCategory = null;
      this.kursusTersaring = this.courses;
    } else {
      this.selectedCategory = kategori;
      this.kursusTersaring = this.courses.filter(
        (k: any) =>
          k.course_type === kategori ||
          k.type === kategori ||
          k.category === kategori
      );
    }
    this.cdr.detectChanges();
  }

  loadCourses() {
    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        const dataAsli = res?.data || res?.courses || res || [];
        this.courses = Array.isArray(dataAsli) ? dataAsli : [];
        this.kursusTersaring = this.courses;
        this.pisahKategoriCourses(this.courses);
        this.cdr.detectChanges();
      },
      error: (err: any) => {
        console.error('Gagal memuat courses di loadCourses:', err);
      },
    });
  }

  ionViewWillEnter() {
    const localUserData =
      localStorage.getItem('user_data') || localStorage.getItem('user');
    if (localUserData) {
      try {
        const user = JSON.parse(localUserData);
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap;
        this.cdr.detectChanges();
      } catch (e) {
        console.error('Gagal parse user data di beranda:', e);
      }
    }

    this.muatJumlahNotifikasi();
    this.muatDataBerandaTotal();
  }

  muatDataBerandaTotal(refresherEvent?: CustomEvent) {
    if (!refresherEvent) {
      this.isLoading = true;
    }

    this.courseService.getCourses().subscribe({
      next: (res: any) => {
        const dataAsli = res?.data || res?.courses || res || [];
        const arrayCourses = Array.isArray(dataAsli) ? dataAsli : [];

        this.courses = arrayCourses;
        this.pisahKategoriCourses(arrayCourses);

        if (this.selectedCategory) {
          this.kursusTersaring = arrayCourses.filter(
            (k: any) =>
              k.course_type === this.selectedCategory ||
              k.type === this.selectedCategory ||
              k.category === this.selectedCategory
          );
        } else {
          this.kursusTersaring = arrayCourses;
        }
      },
      error: (err) => {
        console.error('Gagal memuat data beranda dari server:', err);
        this.isLoading = false;
        if (refresherEvent) {
          (refresherEvent.target as any).complete();
        }
        this.cdr.detectChanges();
      },
      complete: () => {
        this.isLoading = false;
        if (refresherEvent) {
          (refresherEvent.target as any).complete();
        }
        this.cdr.detectChanges();
      },
    });
  }

  private pisahKategoriCourses(allCourses: any[]) {
    this.coursesWajib = allCourses.filter(
      (c: any) =>
        c.type === 'Wajib' ||
        c.course_type === 'Mata Pelajaran Wajib' ||
        c.course_type === 'school' ||
        c.is_wajib === 1
    );

    this.coursesPilihan = allCourses.filter(
      (c: any) =>
        c.type === 'Pilihan' ||
        c.course_type === 'Mata Pelajaran Pilihan' ||
        c.course_type === 'premium' ||
        c.is_wajib === 0
    );
  }

  handleRefresh(event: CustomEvent) {
    this.muatDataBerandaTotal(event);
  }

  muatJumlahNotifikasi() {
    this.courseService.getNotificationsCount().subscribe({
      next: (res: any) => {
        if (res && res.status === 'success') {
          this.unreadCount = res.unread_count;
          this.cdr.detectChanges();
        }
      },
      error: (err: any) => {
        console.error('Gagal memuat jumlah notifikasi:', err);
      },
    });
  }

  ambilNamaUserLive() {
    this.authService.currentUser$.subscribe((user: any) => {
      if (user) {
        const namaLengkap = user.name || user.nama || user.fullname || 'User';
        this.namaUser = namaLengkap;
        this.cdr.detectChanges();
      } else {
        this.namaUser = 'User';
      }
    });
  }

  goToDetail(id?: any) {
    if (id) {
      this.router.navigate(['/course-detail', id]);
    } else {
      this.router.navigate(['/course-detail']);
    }
  }

  goToBannerDetail() {
    this.router.navigate(['/tabs/course']);
  }

  fungsiCariKursus() {
    const keyword = this.keywordPencarian.trim();
    this.searchService.changeKeyword(keyword);
    this.router.navigate(['/tabs/course']);
    this.keywordPencarian = '';
  }

  goToNotif() {
    this.router.navigate(['/notifications']);
  }

  goToCourse() {
    this.router.navigateByUrl('/tabs/course');
  }

  getSubjectIcon(title: string): string {
    if (!title) return 'book-outline';
    const t = title.toLowerCase();

    if (t.includes('mtk') || t.includes('matematika'))
      return 'calculator-outline';
    if (
      t.includes('pkn') ||
      t.includes('pancasila') ||
      t.includes('kewarganegaraan')
    )
      return 'flag-outline';

    if (t.includes('sunda') || t.includes('daerah') || t.includes('jawa'))
      return 'library-outline';
    if (t.includes('indonesia')) return 'journal-outline';
    if (t.includes('inggris') || t.includes('english')) return 'globe-outline';

    if (
      t.includes('ipa') ||
      t.includes('sains') ||
      t.includes('biologi') ||
      t.includes('fisika')
    )
      return 'flask-outline';
    if (t.includes('ips') || t.includes('sejarah') || t.includes('geografi'))
      return 'earth-outline';
    if (t.includes('agama') || t.includes('pai')) return 'ribbon-outline';
    if (t.includes('pjok') || t.includes('olahraga')) return 'fitness-outline';
    if (t.includes('seni') || t.includes('prakarya')) return 'palette-outline';

    return 'book-outline';
  }

  getSubjectColor(title: string): string {
    if (!title) return '#e0f2fe';
    const t = title.toLowerCase();

    if (t.includes('mtk') || t.includes('matematika')) return '#fee2e2';
    if (t.includes('pkn') || t.includes('pancasila')) return '#fef3c7';
    if (t.includes('sunda') || t.includes('indonesia') || t.includes('bahasa'))
      return '#dcfce7';
    if (t.includes('ipa') || t.includes('sains')) return '#f3e8ff';
    if (t.includes('ips') || t.includes('sejarah')) return '#ffedd5';
    if (t.includes('inggris') || t.includes('english')) return '#e0e7ff';

    return '#e0f2fe';
  }

  getDefaultImage(category: string): string {
    if (!category) return 'assets/icon/computer-science.jpeg';
    const kat = category.toLowerCase();
    if (
      kat.includes('computer') ||
      kat.includes('science') ||
      kat.includes('coding')
    ) {
      return 'assets/icon/computer-science.jpeg';
    } else if (
      kat.includes('microsoft') ||
      kat.includes('office') ||
      kat.includes('excel')
    ) {
      return 'assets/icon/microsoft-office.jpeg';
    }
    return 'assets/icon/computer-science.jpeg';
  }

  handleImageError(event: any, category: string) {
    event.target.onerror = null;
    event.target.src = this.getDefaultImage(category);
  }

  bukaChatCS() {
    const pesan = 'Halo Admin EduLearn, saya ingin bertanya mengenai kursus...';
    const nomorWA = '628978665982';
    window.open(
      `https://wa.me/${nomorWA}?text=${encodeURIComponent(pesan)}`,
      '_blank'
    );
  }
}
