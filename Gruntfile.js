module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    makepot: {
        target: {
            options: {
                cwd: '',
                domainPath: '/languages',
                exclude: ['wc-skroutz-analytics.php'],
                include: [],
                mainFile: 'wc-skroutz-analytics.php',
                potComments: '',
                potFilename: 'wc-skroutz-analytics.pot',
                potHeaders: {
                    poedit: true,
                    'x-poedit-keywordslist': true,
                    'x-poedit-country': 'Greece',
                    'last-translator': 'Skroutz SA <analytics@skroutz.gr>',
                    'language-team': 'Skroutz SA <analytics@skroutz.gr>'
                },
                processPot: function( pot ) {
                	var translation,
                        excluded_meta = [
                            'Plugin Name of the plugin/theme',
                            'Plugin URI of the plugin/theme',
                            'Author of the plugin/theme',
                            'Author URI of the plugin/theme'
                        ];

                    for ( translation in pot.translations[''] ) {
                        if ( 'undefined' !== typeof pot.translations[''][ translation ].comments.extracted ) {
                            if ( excluded_meta.indexOf( pot.translations[''][ translation ].comments.extracted ) >= 0 ) {
                                console.log( 'Excluded meta: ' + pot.translations[''][ translation ].comments.extracted );
                                delete pot.translations[''][ translation ];
                            }
                        }
                    }

                    return pot;
                },
                type: 'wp-plugin',
                updateTimestamp: true,
                updatePoFiles: false
            }
        }
    },
    wp_plugin: {
      deploy: {
        options: {
          assets_dir: 'assets/wp',
          deploy_dir: 'deploy',   // Relative path to your deploy directory (required).
          plugin_slug: 'wc-skroutz-analytics',
          svn_username: 'skroutz',
          svn_repository: 'https://plugins.svn.wordpress.org/skroutz-analytics-woocommerce/'
        }
      }
    },
    copy: {
      main: {
        files: [
          {
            expand: true,
            src: ['**', '!tmp/**', '!Gruntfile.js', '!node_modules/**', '!package.json', '!README.md', '!*.sublime*', '!assets/wp/**'],
            dest: 'deploy/'
          },
        ],
      },
    },
    clean: ['deploy/**', 'tmp/**']
  });

  // Load plugins
  grunt.loadNpmTasks('grunt-wp-i18n');
  grunt.loadNpmTasks('grunt-wp-plugin');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-clean');

  grunt.registerTask('deploy', 'Create a deploy dir, deploy to wordpress svn and clean up', function() {
    grunt.log.ok('Start deploying...');
    grunt.task.run(['copy', 'wp_plugin', 'clean']);
    grunt.log.ok('Deploy finished!');
  });
};
