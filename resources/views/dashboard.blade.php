@extends("redis-manager::layout")

@section("scripts")
<script type="text/babel">
  const { useState, useEffect } = React;
  const {
      colors,
      CssBaseline,
      ThemeProvider,
      Typography,
      Container,
      createTheme,
      Box,
      SvgIcon,
      Link,
      AppBar,
      Toolbar,
      Drawer,
      List,
      ListItem ,
      ListItemButton,
      ListItemIcon,
      ListItemText, 
      Divider,
      Paper,
      TableContainer,
      Table,
      TableHead,
      TableRow,
      TableCell,
      TableBody,
      TablePagination,
      Button,
      ButtonGroup,
      Alert,
      Dialog,
      DialogContent,
      DialogContentText,
      DialogActions,
      DialogTitle,
      Snackbar,
      Grid
  } = MaterialUI;

  // Create a theme instance.
  const theme = createTheme({
  palette: {
    primary: {
    main: "#121212",
    },
    secondary: {
    main: "#19857b",
    },
    error: {
    main: colors.red.A400,
    },
  },
  });

  const drawerWidth = 350;
  const toolbarHeight = 56;
  
  const queryPing = () => fetch("/api/redis-manager/ping");
  const fetchAllFolder = () => fetch("/api/redis-manager/all-folder");
  const fetchFolderColumn = (folderName) => fetch(`/api/redis-manager/folder-column/${folderName}`);
  const fetchFolderData = (folderName) => fetch(`/api/redis-manager/folder-data/${folderName}`);
  const fetchFolderDataList = (folderName, params = {}) => fetch(`/api/redis-manager/folder-data-list/${folderName}?${new URLSearchParams(params)}`,);
  const fetchItemInfo = (folderName, params) => fetch(`/api/redis-manager/item-info/${folderName}?${new URLSearchParams(params)}`);

  const flushFolder = (folderName) => fetch(`/api/redis-manager/flush-folder/${folderName}`, { method: 'DELETE'});
  const flushAllFolder = () => fetch("/api/redis-manager/flush-all", { method: 'DELETE'});

  const RootProvider = React.createContext({});

  const FolderIcon = (props) => {
        return (
          <SvgIcon {...props}>
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path>
          </SvgIcon>
        );
      };
    
  const ReloadIcon = (props) => {
    return (
      <SvgIcon {...props}>
      <path d="m19 8-4 4h3c0 3.31-2.69 6-6 6-1.01 0-1.97-.25-2.8-.7l-1.46 1.46C8.97 19.54 10.43 20 12 20c4.42 0 8-3.58 8-8h3l-4-4zM6 12c0-3.31 2.69-6 6-6 1.01 0 1.97.25 2.8.7l1.46-1.46C15.03 4.46 13.57 4 12 4c-4.42 0-8 3.58-8 8H1l4 4 4-4H6z"></path>
      </SvgIcon>
    );
  };
  
  const DeleteIcon = (props) => {
    return (
      <SvgIcon {...props}>
        <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zm2.46-7.12 1.41-1.41L12 12.59l2.12-2.12 1.41 1.41L13.41 14l2.12 2.12-1.41 1.41L12 15.41l-2.12 2.12-1.41-1.41L10.59 14l-2.13-2.12zM15.5 4l-1-1h-5l-1 1H5v2h14V4z"></path>
      </SvgIcon>
    );
  };

  const ViewIcon = (props) => {
    return (
      <SvgIcon {...props}>
      <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"></path>
      </SvgIcon>
    );
  };
  
  const HomeIcon = (props) => {
    return (
      <SvgIcon {...props}>
        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"></path>
      </SvgIcon>
    );
  };


  const HomeApp = (props) => {
    return (
      <h1>🐶: <code>Hello Hoooman!</code></h1>
    )
  };

  const ViewItemDialog = (props) => {
    const {
      open = false,
      onClose,
      item,
      folder
    } = props;
    const { setGlobalLoading } = React.useContext(RootProvider);
    const [itemData, setItemData] = useState({ ttl: 1});

    const handleOnClose = () => {
      if (typeof onClose === 'function') onClose();
    };

    const queryItemInfo = async() => {
      setGlobalLoading(true);

      const result = await fetchItemInfo(folder, item);
      const data = await result.json();

      if (result.ok) {
        setItemData(data);
      }

      setGlobalLoading(false);
    }

    useEffect(() => {
      if(!Boolean(item) || !Boolean(folder)) return;
      queryItemInfo();
    }, [item, folder]);

    return (
      <Dialog open={open} onClose={handleOnClose} maxWidth='lg'>
        <DialogTitle>View item</DialogTitle>
        <Divider />
        <DialogContent >
          <Box width={500}>
            <Grid container gap={2}>
              <Grid item container>
                <Grid item xs={6}><strong>TTL</strong></Grid>
                <Grid item xs={6}>{itemData.ttl} sec</Grid>
              </Grid>
            </Grid>
          </Box>
        </DialogContent>
        <DialogActions>
          <Divider />
          <Button onClick={handleOnClose} variant="contained" color="error">Close</Button>
        </DialogActions>
      </Dialog>
    )
  }

  const MainApp = (props) => {
    const { folder } = props;
    const [ folderColumn, setFolderColumn ] = useState({});
    const [ selectedItem, setSelectedItem ] = useState(null);
    const [ openViewItemDialog, setOpenViewItemDialog ] = useState(null);
    const [ folderData, setFolderData] = useState([]);
    const [ dataTotal, setDataTotal] = useState(0);
    const [ dataModel, setDataModel ] = useState({
      page: 0,
      limit: 15
    });

    const { setGlobalLoading } = React.useContext(RootProvider);

    const queryFolder = async () => {
      setGlobalLoading(true);
      const resultFolderColumn = await fetchFolderColumn(folder);
      const folderColumnData =  await resultFolderColumn.json();
      let data = [];
      let totalData = 0;

      if (resultFolderColumn.ok) {
        const resultFolderData = await fetchFolderDataList(folder, dataModel);
        const folderResultData= await resultFolderData.json();
        data = folderResultData.data || [];
        totalData = folderResultData.total || 0;
      }
      setDataTotal(totalData);
      setFolderData(data);
      setFolderColumn(folderColumnData);
      setGlobalLoading(false);
    }

    const handleDataModel = (params) => setDataModel(prev => ({...prev, ...params}));

    const handleChangePage = (event, newPage) => handleDataModel({page: newPage});
    const handleChangeRowsPerPage = (event, newPage) => handleDataModel({limit: parseInt(event.target.value, 10)});
    const handleOnReloadFolder = (event) => setDataModel({limit: 15, page: 0});

    const handleOnViewItem = (item) => {
      setSelectedItem(item);
      setOpenViewItemDialog(true);
    };

    useEffect(() => {
      if(!folder) return;
      queryFolder();
    }, [folder, dataModel]);

    return (
      <Box display='flex' flexDirection='column' gap={2}>
        {
          folder && (
            <Toolbar component={Paper}>
            <Typography variant="h4" component="div" sx=@{{ flexGrow: 1 }}>
              {folder || ''}
            </Typography>
            <Button onClick={handleOnReloadFolder} color="success" variant="contained" startIcon={<ReloadIcon/>}>Reload</Button>
          </Toolbar>
          )
        }
        {
          folder ? (
            <TableContainer component={Paper}>
              <Table sx=@{{ minWidth: 650 }} aria-label="simple table">
                <TableHead>
                  <TableRow>
                    {
                      Object.entries(folderColumn).map((data, key) => <TableCell key={`table-folder-column-${key}`}><strong>{data[0] || ''} ({data[1] || ''})</strong></TableCell>)
                    }

                    <TableCell key="table-folder-column-action"><strong>action</strong></TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {
                    folderData.map((row, key) => (
                      <TableRow key={`table-folder-container-${key}`}>
                      {
                        Object.keys(folderColumn).map((item, key) => <TableCell key={`table-folder-item-${key}`}>{JSON.stringify(row[item] || 'NULL')}</TableCell>)
                      }
                      <TableCell key="table-folder-item-action"><Button variant="contained" size="small" onClick={() => handleOnViewItem(row)} startIcon={<ViewIcon/>}>Info</Button></TableCell>
                    </TableRow>))
                  }
                </TableBody>
              </Table>
              <TablePagination
                component="div"
                count={dataTotal}
                page={dataModel.page}
                onPageChange={handleChangePage}
                rowsPerPage={dataModel.limit}
                onRowsPerPageChange={handleChangeRowsPerPage}
                rowsPerPageOptions={[15, 100, 1000]}
              />
            </TableContainer>
          ) : (
            <HomeApp />
          )
        }

        <ViewItemDialog
          open={openViewItemDialog}
          onClose={() => setOpenViewItemDialog(false)}
          item={selectedItem}
          folder={folder}
        />
      </Box>
    );
  }

  const Pinger = ({children, handleStatus}) => {
    const [ pinging, setPinging ] = useState(false);
    const [ isErrorShownOnce, setIsErrorShownOnce ] = useState(false);
    const { setStatus } = React.useContext(RootProvider);

    useEffect(() => {
      const pingInterval = setTimeout(async () => {
        if(pinging) return;
        setPinging(true);
        const result = await queryPing();
        setStatus(result.ok);
        const data = await result.json();
        if(!result.ok) {
          if(!isErrorShownOnce) {
            setIsErrorShownOnce(true);
          }
          clearInterval(pingInterval);
        } else {
          setIsErrorShownOnce(false);
        }

        setPinging(false);

      }, 4000);

      return () => {
        clearInterval(pingInterval);
      };
    });

    return (<Box>{children}</Box>);
  }

  const GlobalDialog = ({ open, children }) => {
    return (
      <Dialog disableEscapeKeyDown={true} open={open}>
        <DialogContent>
          { children }      
        </DialogContent>
      </Dialog>
      );
  };


  const ConfirmationDialog = (props) => {
    const {
      message = 'Are you sure?',
      open = false,
      onCancel,
      onConfirm
    } = props;

    const handleOnCancel = () => {
      if (typeof onCancel === 'function') onCancel();
    };

    const handleOnConfirm = (event) => {
      if (typeof onConfirm === 'function') onConfirm(event);
    };

    return (
      <Dialog open={open} onClose={handleOnCancel} >
        <DialogTitle>Confirmation</DialogTitle>
        <Divider />
        <DialogContent >
          <DialogContentText>
            { message }
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleOnConfirm} variant="contained" color="success">Yes, Continue</Button>
          <Button onClick={handleOnCancel} variant="contained" color="error">Cancel</Button>
        </DialogActions>
      </Dialog>
    )
  };

  const BaseProvider = (props) => {
    const {
      onFolderSelected, children,
      online = true
    } = props;
    const [ allFolderData, setAllFolderData ] = useState([]);
    const [ selectedFolder, setSelectedFolder ] = useState('');
    const [ confirmationMessage, setConfirmationMessage ] = useState("");
    const [ openConfirmationDialog, setOpenConfirmationDialog ] = useState(false);
    const [ isDrop, setIsDrop ] = useState(false);

    const { setGlobalLoading, setGlobalSnackbarOpen, setGlobalSnackbarMessage, setGlobalSnackbarSeverity } = React.useContext(RootProvider);

    const queryAllFolder = async () => {
      setGlobalLoading(true);
      const result = await fetchAllFolder();
      const { data= [] } = await result.json();
      setAllFolderData(data);
      setGlobalLoading(false);
    };

    const handleOnFolderSelected = (folderValue, e) => {
      setSelectedFolder(folderValue);
      if (typeof onFolderSelected === 'function') onFolderSelected(folderValue);
    };

    const handleRemoveFolder = (folder) => {
      setIsDrop(false);
      setConfirmationMessage(`Do you want to remove '${folder}'?`);
      setOpenConfirmationDialog(true);
      setSelectedFolder(folder);
    };

    const handleDropAllFolder = () => {
      setIsDrop(true);
      setConfirmationMessage("Do you want to remove all folder?");
      setOpenConfirmationDialog(true);
    };

    const handleOnRemoveFolder = async(event) => {
      setGlobalLoading(true);
      const result = await flushFolder(selectedFolder);
      if (result.ok) {
        setOpenConfirmationDialog(false);
        handleOnFolderSelected(null, event);
        queryAllFolder();
        setGlobalSnackbarMessage("Success: removed folder");
        setGlobalSnackbarSeverity("success");
      } else {
        setGlobalSnackbarSeverity("error");
        setGlobalSnackbarMessage("Error: cannot remove folder");
      }

      setGlobalSnackbarOpen(true);
      setGlobalLoading(false);
    };

    const handleOnDropFolder = async(event) => {
      setGlobalLoading(true);
      const result = await flushAllFolder();
      if (result.ok) {
        setOpenConfirmationDialog(false);
        handleOnFolderSelected(null, event);
        queryAllFolder();
        setGlobalSnackbarMessage("Success: dropped all folder");
        setGlobalSnackbarSeverity("success");
      } else {
        setGlobalSnackbarSeverity("error");
        setGlobalSnackbarMessage("Error: cannot drop all folder");
      }

      setGlobalSnackbarOpen(true);
      setGlobalLoading(false);
    };

    useEffect(() => {
      queryAllFolder();
    }, []);

    return (
      <Box sx=@{{ display: 'flex' }}>
        <CssBaseline />
        <AppBar position="fixed" sx=@{{ zIndex: (theme) => theme.zIndex.drawer + 1 }}>
        <Toolbar>
          <Typography variant="h6" noWrap component="div">
              Laravel Redis Manager | { Boolean(online) ? 'O N L I N E' : 'O F F L I N E'}
          </Typography>
          </Toolbar>
        </AppBar>
        <Drawer
          variant="permanent"
          sx=@{{
            width: drawerWidth,
            flexShrink: 0,
            [`& .MuiDrawer-paper`]: { width: drawerWidth, boxSizing: 'border-box' },
          }}
        >
        <Toolbar/>
        <Toolbar>
        <ButtonGroup variant="outlined" aria-label="outlined primary button group">
          <Button onClick={(event) => handleOnFolderSelected(null, event)} size="small" color="info" variant="contained" startIcon={<HomeIcon/>}>Home</Button>
          <Button onClick={() => handleDropAllFolder()} size="small" color="error" variant="contained" startIcon={<DeleteIcon/>}>Drop</Button>
          <Button onClick={() => queryAllFolder()} color="success" variant="contained" startIcon={<ReloadIcon/>}>Reload</Button>
        </ButtonGroup>
        </Toolbar>
          <Box sx=@{{ overflow: 'auto', padding: 1 }}>
          <List>
            {allFolderData.map((data, index) => (
              <ListItem key={index} disablePadding selected={selectedFolder === data}>
                <ListItemButton onClick={(e) => handleOnFolderSelected(data, e)}>
                  <ListItemIcon>
                    <FolderIcon/>
                  </ListItemIcon>
                  <ListItemText primary={data} />
                  </ListItemButton>
                  <Button onClick={() => handleRemoveFolder(data)} size="small" color="error" variant="contained" startIcon={<DeleteIcon/>}>Remove</Button>
              </ListItem>
            ))}
          </List>
        </Box>
        </Drawer>
          <Box
          component='main'
          sx=@{{
            flexGrow: 1,
            height: '100vh',
            overflow: 'auto',
            }}
          >
          <Toolbar />
          <Box p={2} pt={0}>
          { children }
          </Box>
          <ConfirmationDialog
            message={confirmationMessage}
            open={openConfirmationDialog}
            onCancel={(event) => setOpenConfirmationDialog(false)}
            onConfirm={isDrop ? handleOnDropFolder : handleOnRemoveFolder}
          />
          
        </Box>
      </Box>
    )
  }

  const App = () => {
    const [ folder, setFolder ] = useState(null);
    const [ status, setStatus ] = useState(true);
    const [ globalLoading, setGlobalLoading ] = useState(true);
    const [ globalSnackbarOpen, setGlobalSnackbarOpen ] = useState(false);
    const [ globalSnackbarMessage, setGlobalSnackbarMessage ] = useState("Success");
    const [ globalSnackbarSeverity, setGlobalSnackbarSeverity ] = useState("success");

    return (
      <RootProvider.Provider value=@{{ 
        globalLoading, setGlobalLoading, 
        status, setStatus,  
        globalSnackbarOpen, setGlobalSnackbarOpen,
        globalSnackbarMessage, setGlobalSnackbarMessage,
        globalSnackbarSeverity, setGlobalSnackbarSeverity
      }}>
        <ThemeProvider theme={theme}>
          <Pinger>
            <GlobalDialog open={!Boolean(status) || globalLoading}>
              {
                Boolean(globalLoading) ? 
                (
                  <Alert severity="info"><strong>Please wait...</strong></Alert>
                ) : (
                  !Boolean(status) && (<Alert severity="error"><strong>No Redis connection!!!</strong></Alert>)
                )
              }
            </GlobalDialog>
            <BaseProvider onFolderSelected={(folderValue) => setFolder(folderValue)} online={status}>
              <MainApp folder={folder}/>
              <Snackbar
                open={globalSnackbarOpen}
                autoHideDuration={6000}
                onClose={() => setGlobalSnackbarOpen(false)}
              >
                <Alert severity={globalSnackbarSeverity}><strong>{globalSnackbarMessage}</strong></Alert>
              </Snackbar>
            </BaseProvider>
          </Pinger>
      </ThemeProvider>
      </RootProvider.Provider>
    )
  }

  const root = ReactDOM.createRoot(document.getElementById("root"));
  root.render(<App />);
</script>
@endsection
