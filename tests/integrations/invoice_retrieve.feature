#retrieve_invoices.feature
Feature: retrieving an invoice
  The user may want to retrieve invoices
  So that they can view them

  @javascript
  Scenario: The request is correct
    Given that a user knows an invoice id
    Then they can retrieve that invoice