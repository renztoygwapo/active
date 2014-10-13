<?php

  $company_id = $this->createCompany('A51');
  
  $this->createUser('ilija.studen@gmail.com', $company_id);
  $this->createUser('igor@gmail.com', $company_id);
  $this->createUser('goran.radulovic@gmail.com', $company_id);
  $this->createUser('goranb@gmail.com', $company_id);
  $this->createUser('themaric@gmail.com', $company_id);
  $this->createUser('ivana@gmail.com', $company_id);