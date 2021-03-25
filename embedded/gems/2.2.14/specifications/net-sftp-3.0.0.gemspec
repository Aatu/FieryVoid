# -*- encoding: utf-8 -*-
# stub: net-sftp 3.0.0 ruby lib

Gem::Specification.new do |s|
  s.name = "net-sftp".freeze
  s.version = "3.0.0"

  s.required_rubygems_version = Gem::Requirement.new(">= 0".freeze) if s.respond_to? :required_rubygems_version=
  s.require_paths = ["lib".freeze]
  s.authors = ["Jamis Buck".freeze, "Delano Mandelbaum".freeze, "Mikl\u00F3s Fazekas".freeze]
  s.bindir = "exe".freeze
  s.cert_chain = ["-----BEGIN CERTIFICATE-----\nMIIDQDCCAiigAwIBAgIBATANBgkqhkiG9w0BAQsFADAlMSMwIQYDVQQDDBpuZXRz\nc2gvREM9c29sdXRpb3VzL0RDPWNvbTAeFw0yMDA0MjYwNjI0NTFaFw0yMTA0MjYw\nNjI0NTFaMCUxIzAhBgNVBAMMGm5ldHNzaC9EQz1zb2x1dGlvdXMvREM9Y29tMIIB\nIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAxieE22fR/qmdPKUHyYTyUx2g\nwskLwrCkxay+Tvc97ZZUOwf85LDDDPqhQaTWLvRwnIOMgQE2nBPzwalVclK6a+pW\nx/18KDeZY15vm3Qn5p42b0wi9hUxOqPm3J2hdCLCcgtENgdX21nVzejn39WVqFJO\nlntgSDNW5+kCS8QaRsmIbzj17GKKkrsw39kiQw7FhWfJFeTjddzoZiWwc59KA/Bx\nfBbmDnsMLAtAtauMOxORrbx3EOY7sHku/kSrMg3FXFay7jc6BkbbUij+MjJ/k82l\n4o8o0YO4BAnya90xgEmgOG0LCCxRhuXQFnMDuDjK2XnUe0h4/6NCn94C+z9GsQID\nAQABo3sweTAJBgNVHRMEAjAAMAsGA1UdDwQEAwIEsDAdBgNVHQ4EFgQUBfKiwO2e\nM4NEiRrVG793qEPLYyMwHwYDVR0RBBgwFoEUbmV0c3NoQHNvbHV0aW91cy5jb20w\nHwYDVR0SBBgwFoEUbmV0c3NoQHNvbHV0aW91cy5jb20wDQYJKoZIhvcNAQELBQAD\nggEBAHZOpj9ZNefc4eoie/20zX4iOgk3i+Rmjo04sMYWEtj0+4zLHXqSjune7+hd\nruP+u5xmEy9ueGQ/0kcLlDDM+8eQo6gjCY4GIQtwAzVLsUVeejE4291/GyfPf24v\n1dLWRtPwth0nyN5EOwOPqeiYiLAr5lO3wHTgfIgFhvdZpcXG8zB6JojuFJjDpKvO\nHBV0P82HXjocnjusBt1YykGCvK4cDnaRruZJh6w2XeYQJJjlbqbDfBSQVesg97yM\nbwwLGaqsVz0cC6q3EpU57oyyTu529o0HnDatc94Cz8t9p6ogkKtSM+jwsnnC6wmI\nMMzdZVhRg3QdHAGYfd+TfsS3Mps=\n-----END CERTIFICATE-----\n".freeze]
  s.date = "2020-04-27"
  s.description = "A pure Ruby implementation of the SFTP client protocol".freeze
  s.email = ["net-ssh@solutious.com".freeze]
  s.extra_rdoc_files = ["LICENSE.txt".freeze, "README.rdoc".freeze]
  s.files = ["LICENSE.txt".freeze, "README.rdoc".freeze]
  s.homepage = "https://github.com/net-ssh/net-sftp".freeze
  s.licenses = ["MIT".freeze]
  s.rubygems_version = "3.0.3".freeze
  s.summary = "A pure Ruby implementation of the SFTP client protocol.".freeze

  s.installed_by_version = "3.0.3" if s.respond_to? :installed_by_version

  if s.respond_to? :specification_version then
    s.specification_version = 3

    if Gem::Version.new(Gem::VERSION) >= Gem::Version.new('1.2.0') then
      s.add_runtime_dependency(%q<net-ssh>.freeze, [">= 5.0.0", "< 7.0.0"])
      s.add_development_dependency(%q<minitest>.freeze, [">= 5"])
      s.add_development_dependency(%q<mocha>.freeze, [">= 0"])
    else
      s.add_dependency(%q<net-ssh>.freeze, [">= 5.0.0", "< 7.0.0"])
      s.add_dependency(%q<minitest>.freeze, [">= 5"])
      s.add_dependency(%q<mocha>.freeze, [">= 0"])
    end
  else
    s.add_dependency(%q<net-ssh>.freeze, [">= 5.0.0", "< 7.0.0"])
    s.add_dependency(%q<minitest>.freeze, [">= 5"])
    s.add_dependency(%q<mocha>.freeze, [">= 0"])
  end
end
