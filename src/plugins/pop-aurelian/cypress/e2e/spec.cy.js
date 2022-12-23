describe('empty spec', () => {
  it('passes', () => {
    cy.visit('http://localhost');
  });
});

context( 'Login, set and prep cookies' , () => {

  it( 'Ensure no one is logged in', () => {
    cy.clearWordPressCookies();
    cy.visit( Cypress.env('dashboardUrl') );
    cy.location('pathname').should('eq', '/wp-login.php' ); // Not logged in
  });


  it( 'Logs in a admin user', ()=> {
    cy.manualWordPressLogin();
    cy.getWordPressCookies();
    cy.visit( Cypress.env('dashboardUrl') );
    cy.location('pathname').should( 'match', /^\/wp-admin/ );
  });


  it( 'logs out the user - and logs back in using setting wp-cookies', ()=> {
    cy.clearWordPressCookies();
    cy.visit( Cypress.env('dashboardUrl') );
    cy.location('pathname').should( 'match', /^\/wp-login\.php/ ); // Not logged in
    cy.setWordPressCookies();
    cy.visit( Cypress.env('dashboardUrl') );
    cy.location('pathname').should( 'match', /^\/wp-admin/ ); // Is logged in
  });

});