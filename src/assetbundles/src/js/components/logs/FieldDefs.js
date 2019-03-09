export default [
  {
    name: 'title',
  }, 
  {
    name: 'elementTypeDisplayName',
    title: 'Type',
    sortField: 'elementTypeDisplayName',
  },
  {
    name: 'action',
    sortField: 'action',
    callback: 'humanize',
  },
  {
    name: 'user.username',
    title: 'User',
    sortField: 'userId',
  },
  {
    name: 'dateCreated',
    title: 'Date',
    sortField: 'dateCreated',
    callback: 'formatDateTime',
  },
  {
    name: 'viewLink',
    title: '',
    callback: 'addViewLink',
  }
];