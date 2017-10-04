/*global module:false*/
module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    // Metadata.
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
      '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
      '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
      '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
      ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
    // Task configuration.
    sass: {
      options: {
        sourceMap: true
      },
      dist: {
        files: {
          'css/custom.css': 'sass/custom.scss',
          'lib/external-media/css/external-media-public.css' : 'lib/external-media/sass/external-media-public.scss',
          'lib/external-media/css/external-media-admin.css' : 'lib/external-media/sass/external-media-admin.scss'
        }
      }
    },
    coffee: {
      compile: {
        files: {
          'js/custom.js': ['coffee/*.coffee'] // compile and concat into single file
        }
      },
    },
    concat: {
      options: {
        stripBanners: true
      },
      styles: {
        src: ['css/custom.css', 'bower_components/tether-tooltip/dist/css/tooltip-theme-arrows.css'],
        dest: 'css/main.css'
      },
      scripts: {
        src: [
        'bower_components/list.pagination.js/dist/list.pagination.min.js',
        'bower_components/list.js/dist/list.min.js',
        'bower_components/tether/dist/js/tether.min.js',
        'bower_components/tether-drop/dist/js/drop.min.js',
        'bower_components/tether-tooltip/dist/js/tooltip.min.js',
        'js/custom.js',
        'js/tome-external-media.js'
        ],
        dest: 'js/dist/master.js'
      }
    },
    autoprefixer: {
      no_dest_single: {
        src: 'css/custom.css'
      },
    },
    uglify: {
      options: {
        banner: '<%= banner %>'
      },
      dist: {
        src: '<%= concat.dist.dest %>',
        dest: 'css/<%= pkg.name %>.min.js'
      }
    },
    jshint: {
      options: {
        curly: true,
        eqeqeq: true,
        immed: true,
        latedef: true,
        newcap: true,
        noarg: true,
        sub: true,
        undef: true,
        unused: true,
        boss: true,
        eqnull: true,
        browser: true,
        globals: {
          jQuery: true
        }
      },
      gruntfile: {
        src: 'Gruntfile.js'
      },
    },
    watch: {
      livereload: {
        options: { livereload: true },
        files: ['css/custom.css'],
      },
      gruntfile: {
        files: '<%= jshint.gruntfile.src %>',
        tasks: ['jshint:gruntfile']
      },
      sass: {
        files: ['sass/*.scss'],
        tasks: ['sass', 'autoprefixer', 'concat:styles'],
      },
      coffee: {
        files: '**/*.coffee',
        tasks: ['coffee', 'concat:scripts'],
      },
      scripts: {
        files: ['js/tome-edit.js', 'js/tome-external-media.js', 'js/custom.js'],
        tasks: ['concat:scripts']
      },
      externalMedia: {
        files: ['lib/external-media/sass/**/*.scss'],
        tasks: ['sass', 'autoprefixer'],
      }
    }
  });

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-qunit');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-coffee');
  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-autoprefixer');

  // Default task.
  grunt.registerTask('default', ['watch', 'concat', 'uglify']);
  grunt.registerTask('build', ['jshint']);
  grunt.registerTask('scripts', ['concat:scripts']);

};
