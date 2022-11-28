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
      Icon
  } = MaterialUI;

  console.log(MaterialUI)

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
  const drawerWidth = 240;

  function FolderIcon(props) {
        return (
          <SvgIcon {...props}>
            <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"></path>
          </SvgIcon>
        );
      }
  
  function DrawerApp() {
    const [ folder, setFolder ] = useState([]);
    const [ isLoading, setIsLoading ] = useState(false);
    const [ selectedFolder, setSelectedFolder ] = useState('');

    const queryAllFolder = async () => {
      setIsLoading(true);
      const result = await fetch("/api/redis-manager/all-folder");
      const { data= [] } = await result.json();
      setFolder(data);
      setIsLoading(false);
    }

    const handleOnFolderSelected = (folderValue, e) => {
      console.log(folderValue);
      setSelectedFolder(folderValue);
    };

    useEffect(() => {
      queryAllFolder();
    }, []);

    return (
      <Drawer
        variant="permanent"
        sx=@{{
        width: drawerWidth,
        flexShrink: 0,
        [`& .MuiDrawer-paper`]: { width: drawerWidth, boxSizing: "border-box" },
        }}
        >
        <Toolbar />
        {
          isLoading && <h1>Loading...</h1>
        }
          <Box>
            <List>
              {folder.map((data, index) => (
                <ListItem key={index} disablePadding selected={selectedFolder === data}>
                  <ListItemButton onClick={(e) => handleOnFolderSelected(data, e)}>
                    <ListItemIcon>
                      <FolderIcon/>
                    </ListItemIcon>
                    <ListItemText primary={data} />
                  </ListItemButton>
                </ListItem>
              ))}
            </List>
          </Box>
        </Drawer>
    );
  }

  // function Main

  function App() {
    const [ pinging, setPinging ] = useState(false);
    const [ isErrorShownOnce, setIsErrorShownOnce ] = useState(false);

    const queryPing = () => fetch("/api/redis-manager/ping");

    useEffect(() => {
      const pingInterval = setTimeout(async () => {
        if(pinging) return;
        setPinging(true);
        const result = await queryPing();
        const data = await result.json();
        if(!result.ok) {
          if(!isErrorShownOnce) {
            alert(data.message || "No redis connection.");
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


    return (
      <Box>
        <AppBar position="fixed" sx=@{{ zIndex: (theme) => theme.zIndex.drawer + 1 }}>
          <Toolbar>
            <Typography variant="h6" noWrap component="div">
                {{ config("app.name", "Laravel")  }}  - Redis Manager
            </Typography>
            </Toolbar>
          </AppBar>
          <Box component="main" sx=@{{ flexGrow: 1, p: 3 }}>
            <Toolbar />
            <Typography paragraph>
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
              tempor incididunt ut labore et dolore magna aliqua. Rhoncus dolor purus non
              enim praesent elementum facilisis leo vel. Risus at ultrices mi tempus
              imperdiet. Semper risus in hendrerit gravida rutrum quisque non tellus.
              Convallis convallis tellus id interdum velit laoreet id donec ultrices.
              Odio morbi quis commodo odio aenean sed adipiscing. Amet nisl suscipit
              adipiscing bibendum est ultricies integer quis. Cursus euismod quis viverra
              nibh cras. Metus vulputate eu scelerisque felis imperdiet proin fermentum
              leo. Mauris commodo quis imperdiet massa tincidunt. Cras tincidunt lobortis
              feugiat vivamus at augue. At augue eget arcu dictum varius duis at
              consectetur lorem. Velit sed ullamcorper morbi tincidunt. Lorem donec massa
              sapien faucibus et molestie ac.
            </Typography>
            <Typography paragraph>
              Consequat mauris nunc congue nisi vitae suscipit. Fringilla est ullamcorper
              eget nulla facilisi etiam dignissim diam. Pulvinar elementum integer enim
              neque volutpat ac tincidunt. Ornare suspendisse sed nisi lacus sed viverra
              tellus. Purus sit amet volutpat consequat mauris. Elementum eu facilisis
              sed odio morbi. Euismod lacinia at quis risus sed vulputate odio. Morbi
              tincidunt ornare massa eget egestas purus viverra accumsan in. In hendrerit
              gravida rutrum quisque non tellus orci ac. Pellentesque nec nam aliquam sem
              et tortor. Habitant morbi tristique senectus et. Adipiscing elit duis
              tristique sollicitudin nibh sit. Ornare aenean euismod elementum nisi quis
              eleifend. Commodo viverra maecenas accumsan lacus vel facilisis. Nulla
              posuere sollicitudin aliquam ultrices sagittis orci a.
            </Typography>
          </Box>
        <DrawerApp/>
    </Box>
    )
  };

  const root = ReactDOM.createRoot(document.getElementById("root"));
  root.render(
    <ThemeProvider theme={theme}>
        <CssBaseline />
        <App />
    </ThemeProvider>
  );
</script>
@endsection
