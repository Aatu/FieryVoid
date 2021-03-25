# -*- encoding: utf-8 -*-
# stub: vagrant 2.2.14 ruby lib

Gem::Specification.new do |s|
  s.name = "vagrant".freeze
  s.version = "2.2.14"

  s.required_rubygems_version = Gem::Requirement.new(">= 1.3.6".freeze) if s.respond_to? :required_rubygems_version=
  s.require_paths = ["lib".freeze]
  s.authors = ["Mitchell Hashimoto".freeze, "John Bender".freeze]
  s.date = "2020-11-20"
  s.description = "Vagrant is a tool for building and distributing virtualized development environments.".freeze
  s.email = ["mitchell.hashimoto@gmail.com".freeze, "john.m.bender@gmail.com".freeze]
  s.executables = ["vagrant".freeze]
  s.files = ["bin/vagrant".freeze]
  s.homepage = "https://www.vagrantup.com".freeze
  s.licenses = ["MIT".freeze]
  s.required_ruby_version = Gem::Requirement.new(["~> 2.5".freeze, "< 2.8".freeze])
  s.rubygems_version = "3.0.3".freeze
  s.summary = "Build and distribute virtualized development environments.".freeze

  s.installed_by_version = "3.0.3" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 4

    if Gem::Version.new(Gem::VERSION) >= Gem::Version.new('1.2.0') then
      s.add_runtime_dependency(%q<bcrypt_pbkdf>.freeze, ["~> 1.0.0"])
      s.add_runtime_dependency(%q<childprocess>.freeze, ["~> 4.0.0"])
      s.add_runtime_dependency(%q<ed25519>.freeze, ["~> 1.2.4"])
      s.add_runtime_dependency(%q<erubi>.freeze, [">= 0"])
      s.add_runtime_dependency(%q<hashicorp-checkpoint>.freeze, ["~> 0.1.5"])
      s.add_runtime_dependency(%q<i18n>.freeze, ["~> 1.8"])
      s.add_runtime_dependency(%q<listen>.freeze, ["~> 3.1"])
      s.add_runtime_dependency(%q<log4r>.freeze, ["~> 1.1.9", "< 1.1.11"])
      s.add_runtime_dependency(%q<mime-types>.freeze, ["~> 3.3"])
      s.add_runtime_dependency(%q<net-ssh>.freeze, [">= 6.2.0.rc1", "< 7"])
      s.add_runtime_dependency(%q<net-sftp>.freeze, ["~> 3.0"])
      s.add_runtime_dependency(%q<net-scp>.freeze, ["~> 1.2.0"])
      s.add_runtime_dependency(%q<rb-kqueue>.freeze, ["~> 0.2.0"])
      s.add_runtime_dependency(%q<rubyzip>.freeze, ["~> 2.0"])
      s.add_runtime_dependency(%q<vagrant_cloud>.freeze, ["~> 3.0.2"])
      s.add_runtime_dependency(%q<wdm>.freeze, ["~> 0.1.0"])
      s.add_runtime_dependency(%q<winrm>.freeze, [">= 2.3.4", "< 3.0"])
      s.add_runtime_dependency(%q<winrm-elevated>.freeze, [">= 1.2.1", "< 2.0"])
      s.add_runtime_dependency(%q<winrm-fs>.freeze, [">= 1.3.4", "< 2.0"])
      s.add_runtime_dependency(%q<ruby_dep>.freeze, ["<= 1.3.1"])
      s.add_development_dependency(%q<rake>.freeze, ["~> 12.3.3"])
      s.add_development_dependency(%q<rspec>.freeze, ["~> 3.5.0"])
      s.add_development_dependency(%q<rspec-its>.freeze, ["~> 1.3.0"])
      s.add_development_dependency(%q<webmock>.freeze, ["~> 2.3.1"])
      s.add_development_dependency(%q<fake_ftp>.freeze, ["~> 0.1.1"])
    else
      s.add_dependency(%q<bcrypt_pbkdf>.freeze, ["~> 1.0.0"])
      s.add_dependency(%q<childprocess>.freeze, ["~> 4.0.0"])
      s.add_dependency(%q<ed25519>.freeze, ["~> 1.2.4"])
      s.add_dependency(%q<erubi>.freeze, [">= 0"])
      s.add_dependency(%q<hashicorp-checkpoint>.freeze, ["~> 0.1.5"])
      s.add_dependency(%q<i18n>.freeze, ["~> 1.8"])
      s.add_dependency(%q<listen>.freeze, ["~> 3.1"])
      s.add_dependency(%q<log4r>.freeze, ["~> 1.1.9", "< 1.1.11"])
      s.add_dependency(%q<mime-types>.freeze, ["~> 3.3"])
      s.add_dependency(%q<net-ssh>.freeze, [">= 6.2.0.rc1", "< 7"])
      s.add_dependency(%q<net-sftp>.freeze, ["~> 3.0"])
      s.add_dependency(%q<net-scp>.freeze, ["~> 1.2.0"])
      s.add_dependency(%q<rb-kqueue>.freeze, ["~> 0.2.0"])
      s.add_dependency(%q<rubyzip>.freeze, ["~> 2.0"])
      s.add_dependency(%q<vagrant_cloud>.freeze, ["~> 3.0.2"])
      s.add_dependency(%q<wdm>.freeze, ["~> 0.1.0"])
      s.add_dependency(%q<winrm>.freeze, [">= 2.3.4", "< 3.0"])
      s.add_dependency(%q<winrm-elevated>.freeze, [">= 1.2.1", "< 2.0"])
      s.add_dependency(%q<winrm-fs>.freeze, [">= 1.3.4", "< 2.0"])
      s.add_dependency(%q<ruby_dep>.freeze, ["<= 1.3.1"])
      s.add_dependency(%q<rake>.freeze, ["~> 12.3.3"])
      s.add_dependency(%q<rspec>.freeze, ["~> 3.5.0"])
      s.add_dependency(%q<rspec-its>.freeze, ["~> 1.3.0"])
      s.add_dependency(%q<webmock>.freeze, ["~> 2.3.1"])
      s.add_dependency(%q<fake_ftp>.freeze, ["~> 0.1.1"])
    end
  else
    s.add_dependency(%q<bcrypt_pbkdf>.freeze, ["~> 1.0.0"])
    s.add_dependency(%q<childprocess>.freeze, ["~> 4.0.0"])
    s.add_dependency(%q<ed25519>.freeze, ["~> 1.2.4"])
    s.add_dependency(%q<erubi>.freeze, [">= 0"])
    s.add_dependency(%q<hashicorp-checkpoint>.freeze, ["~> 0.1.5"])
    s.add_dependency(%q<i18n>.freeze, ["~> 1.8"])
    s.add_dependency(%q<listen>.freeze, ["~> 3.1"])
    s.add_dependency(%q<log4r>.freeze, ["~> 1.1.9", "< 1.1.11"])
    s.add_dependency(%q<mime-types>.freeze, ["~> 3.3"])
    s.add_dependency(%q<net-ssh>.freeze, [">= 6.2.0.rc1", "< 7"])
    s.add_dependency(%q<net-sftp>.freeze, ["~> 3.0"])
    s.add_dependency(%q<net-scp>.freeze, ["~> 1.2.0"])
    s.add_dependency(%q<rb-kqueue>.freeze, ["~> 0.2.0"])
    s.add_dependency(%q<rubyzip>.freeze, ["~> 2.0"])
    s.add_dependency(%q<vagrant_cloud>.freeze, ["~> 3.0.2"])
    s.add_dependency(%q<wdm>.freeze, ["~> 0.1.0"])
    s.add_dependency(%q<winrm>.freeze, [">= 2.3.4", "< 3.0"])
    s.add_dependency(%q<winrm-elevated>.freeze, [">= 1.2.1", "< 2.0"])
    s.add_dependency(%q<winrm-fs>.freeze, [">= 1.3.4", "< 2.0"])
    s.add_dependency(%q<ruby_dep>.freeze, ["<= 1.3.1"])
    s.add_dependency(%q<rake>.freeze, ["~> 12.3.3"])
    s.add_dependency(%q<rspec>.freeze, ["~> 3.5.0"])
    s.add_dependency(%q<rspec-its>.freeze, ["~> 1.3.0"])
    s.add_dependency(%q<webmock>.freeze, ["~> 2.3.1"])
    s.add_dependency(%q<fake_ftp>.freeze, ["~> 0.1.1"])
  end
end
