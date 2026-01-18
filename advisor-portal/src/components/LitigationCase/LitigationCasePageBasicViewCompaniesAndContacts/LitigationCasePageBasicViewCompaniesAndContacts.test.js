import React from 'react';
import ReactDOM from 'react-dom';
import LitigationCasePageBasicViewCompaniesAndContacts from './LitigationCasePageBasicViewCompaniesAndContacts';

it('It should mount', () => {
  const div = document.createElement('div');
  ReactDOM.render(<LitigationCasePageBasicViewCompaniesAndContacts />, div);
  ReactDOM.unmountComponentAtNode(div);
});